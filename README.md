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

## Requisitos atendidos
- Formulário único para login e criação de conta.
- Alteração visual por modo:
  - Azul: login
  - Verde: criar conta
- Apenas dois campos: usuário e senha.
- Senha criptografada com `Argon2id` com parâmetros fortes.
