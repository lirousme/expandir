# Sistema de Login Minimalista (POO + MVC + DDD)

## Stack
- JavaScript puro
- PHP
- MySQL
- TailwindCSS em dark mode

## Estrutura
- `src/Domain`: entidades e contratos
- `src/Application`: casos de uso (login/cadastro)
- `src/Infrastructure`: persistência MySQL e conexão
- `src/Presentation`: controller HTTP
- `public`: interface e endpoints

## Configuração
1. Copie `.env.example` para `.env` e ajuste credenciais.
2. Crie o banco e rode o script `database/schema.sql`.
3. Inicie o servidor:

```bash
php -S 127.0.0.1:8000 -t public
```

### Timezone padrão (Brasília)
- O sistema PHP usa `APP_TIMEZONE` (padrão: `America/Sao_Paulo`).
- Cada conexão MySQL aplica `SET time_zone` com `DB_TIMEZONE_OFFSET` (padrão: `-03:00`).
- A coluna `users.created_at` é `DATETIME` (sem conversão automática de fuso do MySQL), e o valor é salvo pela aplicação já no fuso de Brasília.
- Assim, datas geradas pela aplicação e persistidas no banco respeitam horário de Brasília por padrão.

#### Migração para quem já criou a tabela
Se a tabela `users` já existe com `created_at` como `TIMESTAMP`, rode:

```sql
ALTER TABLE users MODIFY created_at DATETIME NOT NULL;
```

## Requisitos atendidos
- Formulário único para login e criação de conta.
- Alteração visual por modo:
  - Azul: login
  - Verde: criar conta
- Apenas dois campos: usuário e senha.
- Senha criptografada com `Argon2id` com parâmetros fortes.


## Deploy em subpasta (`public_html/login`)
- O projeto já inclui `index.php` e `auth.php` na raiz do repositório como ponte para a pasta `public`.
- Assim, ao publicar em `public_html/login`, a URL `https://seusite.com/login/` funciona sem precisar acessar `/login/public`.
