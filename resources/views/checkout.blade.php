@extends('layouts.front')
@section('content')
    <div class="container">
        <div class="col-md-6">
            <h4>Dados do Cartão</h4>
            <hr>
            <form class="cc-form">
                <div class="clearfix">
                    <div class="form-group form-group-cc-number">
                        <label>Nº Cartão</label><span class="brand"></span>
                        <input class="form-control" placeholder="xxxx xxxx xxxx xxxx" type="text" name="card_number" />
                    </div>
                    <div class="form-group form-group-cc-cvc">
                        <label>Cód. Segurança</label>
                        <input class="form-control" placeholder="xxxx" type="text" name="card_cvv" />
                    </div>
                </div>
                <div class="clearfix">
                    <div class="form-group form-group-cc-name">
                        <label>Nome no Cartão</label>
                        <input class="form-control" type="text" name="name"/>
                    </div>
                    <div class="form-group form-group-cc-date">
                        <label>Validade</label>
                        <input class="form-control" placeholder="mm/yy" type="text" name="card_valid" />
                    </div>
                    <div class="form-group installments">
                    </div>
                </div>
                <button class="btn btn-success btn-lg">EFETUAR</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>
    <script>
        const sessionId = '{{session()->get('pagseguro_session_code')}}';
        PagSeguroDirectPayment.setSessionId(sessionId);
    </script>
    <script>
        let cardNumber = document.querySelector('input[name=card_number]');
        let spanBrand = document.querySelector('span.brand');
        cardNumber.addEventListener('keyup', function(){
            if(cardNumber.value.length >= 6)
            {
                PagSeguroDirectPayment.getBrand({
                    cardBin: cardNumber.value.substr(0,6),
                    success: function(res)
                    {
                        let imgFlag = `<img src="https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/68x30/${res.brand.name}.png"></img>`
                        spanBrand.innerHTML = imgFlag

                        getInstallments(40, res.brand.name)
                    },
                    error: function(err){
                        console.log(err)
                    },
                    complete: function(res){
                        console.log('Complete', res)
                    }
                });
            }
        });

        function getInstallments(amount, brand){
            PagSeguroDirectPayment.getInstallments({
                amount: amount,
                brand: brand,
                maxInstallmentNoInterests: 0,
                success: function(res){ 
                    let selectInstallments = drawSelectInstallments(res.installments[brand])
                    document.querySelector('div.installments').innerHTML = selectInstallments
                },
                error: function(res){
                    console.log(err)
                },
                complete: function(res){

                },
            })
        }
        function drawSelectInstallments(installments) {
		let select = '<label>Opções de Parcelamento:</label>';
		select += '<select class="form-control">';
		for(let l of installments) {
		    select += `<option value="${l.quantity}|${l.installmentAmount}">${l.quantity}x de ${l.installmentAmount} - Total fica ${l.totalAmount}</option>`;
		}
		select += '</select>';
		return select;
	    }
    </script>
@endsection