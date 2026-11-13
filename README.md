# Marketplace Feirão da Fotografia!

Marketplace de produtos de fotografia, criado originalmente no curso da CodeExperts "Laravel 6 na prática" e migrado para o Laravel 13.

## Tecnologias

- **Backend:** Laravel 13 (PHP 8.3+)
- **Banco de dados:** SQLite por padrão (MySQL também funciona, basta ajustar o `.env`)
- **Frontend:** Blade, Bootstrap 5, Bootstrap Icons e Inputmask, compilados com Vite
- **Autenticação:** laravel/ui

## Funcionalidades

- Vitrine com produtos, categorias e lojas
- Carrinho de compras na sessão
- Fechamento de pedido (sem gateway de pagamento: o pedido é criado como pendente)
- Painel do dono da loja: loja, produtos com fotos, categorias, pedidos recebidos e notificações

## Como rodar

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Acesse http://localhost:8000.

### Usuários de demonstração

O seeder cria estes usuários, todos com a senha `password`:

| Perfil | E-mail |
|---|---|
| Dono de loja | `loja@example.com` |
| Cliente | `cliente@example.com` |

Quem se cadastra pelo site entra como cliente. Para transformar alguém em dono de loja:

```bash
php artisan tinker --execute="App\Models\User::where('email', 'email@exemplo.com')->update(['role' => 'ROLE_OWNER']);"
```

## Testes

```bash
php artisan test
```
