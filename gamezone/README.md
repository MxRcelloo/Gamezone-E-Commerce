# GameZone — E-commerce acadêmico

Projeto de e-commerce desenvolvido com PHP, MySQL/MariaDB, PDO, HTML, CSS, Bootstrap e JavaScript.

## Requisitos
- XAMPP com Apache e MySQL ativos
- PHP 8.1 ou superior (extensões PDO e pdo_mysql habilitadas)
- MySQL ou MariaDB

## Instalação no XAMPP
1. Extraia a pasta `gamezone` em `C:/xampp/htdocs/`.
2. Inicie Apache e MySQL pelo painel do XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe o arquivo `database.sql` na aba Importar (ele cria o banco `gamezone`).
5. Copie `.env.example` para `.env` e confira as credenciais do seu MySQL.
6. Acesse `http://localhost/gamezone`.

## Criar o primeiro administrador
1. Cadastre uma conta normalmente pela tela "Criar conta".
2. No phpMyAdmin, selecione o banco `gamezone`, abra a aba SQL e execute, substituindo o e-mail pelo e-mail cadastrado:

```sql
UPDATE users SET role = 'admin' WHERE email = 'seuemail@exemplo.com';
```

3. Saia e entre novamente. O link "Admin" aparecerá na navegação.
4. Acesse `http://localhost/gamezone/admin`.

## Funcionalidades
- Catálogo dinâmico, busca e filtro por categoria
- Página de detalhes, preço e estoque
- Cadastro e login com `password_hash()` / `password_verify()`
- Carrinho em sessão, atualização de quantidades e remoção
- Checkout autenticado com seleção de PIX, boleto ou cartão (simulação)
- Pedido, itens do pedido, transação e baixa de estoque
- Painel administrativo protegido por sessão e perfil
- CRUD de produtos e consulta de pedidos/itens

## Segurança implementada
- PDO com prepared statements
- Credenciais fora do código, via `.env`
- `.env` no `.gitignore` e bloqueio de acesso pelo `.htaccess`
- Senhas armazenadas com hash
- Tokens CSRF nos formulários de alteração
- Regeneração do ID da sessão no login/cadastro
- Controle de acesso administrativo no servidor
- Checkout transacional com bloqueio de estoque (`FOR UPDATE`)

## Observações
- O pagamento é apenas uma simulação acadêmica. Nenhuma cobrança real é realizada.
- As imagens de demonstração usam URLs externas; é necessário acesso à internet para carregá-las.
- Para publicação em hospedagem real, configure HTTPS, permissões e credenciais próprias, além de revisar a política de erros e uploads.
