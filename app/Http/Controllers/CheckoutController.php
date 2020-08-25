<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment\PagSeguro\CreditCard;
use App\Payment\PagSeguro\Notification;
use App\Store;
use App\UserOrder;
use Ramsey\Uuid\Uuid;


class CheckoutController extends Controller
{
    public function index()
    {
        if(!auth()->check())
        {
            return redirect()->route('login');
        }

        if(!session()->has('cart')) return redirect()->route('home');

        $this->makePagSeguroSession();

        $cartItems = array_map(function($line){
            return $line['amount'] * $line['price'];
        }, session()->get('cart'));

        $cartItems = array_sum($cartItems);

        return view('checkout', compact('cartItems'));
    }

    public function process(Request $request)
    {
        try
        {
            $dataPost = $request->all();
            $user = auth()->user();
            $cartItems = session()->get('cart');
            $stores = array_unique(array_column($cartItems, 'store_id'));
            $reference = Uuid::uuid4();

            $creditCardPayment = new CreditCard($cartItems, $user, $dataPost, $reference);
            $result = $creditCardPayment->doPayment();

            $userOrder = [
                'reference' => $reference,
                'pagseguro_code' => $result->getCode(),
                'pagseguro_status' => $result->getStatus(),
                'items' => serialize($cartItems),
            ];

            $userOrder = $user->orders()->create($userOrder);
            $userOrder->stores()->sync($stores);

            // ##NOTIFICAÇÃO DE NOVOS PEDIDOS##
            $store = (new Store())->notifyStoreOwners($stores);
            
            session()->forget('cart');
            session()->forget('pagseguro_session_code');

            return response()->json([
                'data' => [
                    'status'  => true,
                    'message' => 'Pedido criado com sucesso!',
                    'order'   => $reference
                ]
            ]);
        }catch(\Exception $e){
            
            $message = env('APP_DEBUG') ? $e->getMessage() : 'Erro ao processar!';
            return response()->json([
                'data' => [
                    'status' => false,
                    'message' => 'Erro ao Processar!'
                ]
                ], 401);
        };
    }

    public function thanks()
    {
        return view('thanks');
    }

    public function notification()
    {
        try{
            $notification = new Notification();
            $notification = $notification->getTransaction();
dd($notification);
            //Atualizar o pedido
            $reference = base64_decode($notification->getReference());
            $userOrder = UserOrder::whereReference($reference);
            $userOrder->update([
                'pagseguro_status' => $notification->getStatus()
            ]);

                //comentários pedido pago
                if($notification->getStatus() == 3){
                    //
                }
                return response()->json(['foi'], 204);
            }catch(\Exception $e){
                $message = env('APP_DEBUG') ? $e->getMessage(): '';
                return response()->json(['error'=>$message], 500);
        }
    }

    private function makePagSeguroSession()
    {
        if(!session()->has('pagseguro_session_code'))
        {
            $sessionCode = \PagSeguro\Services\Session::create(
                \PagSeguro\Configuration\Configure::getAccountCredentials()
            );

            return session()->put('pagseguro_session_code', $sessionCode->getResult());
        }
    }
}
