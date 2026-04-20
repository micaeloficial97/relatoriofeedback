# Relatorio Feedback

Painel web em PHP para visualizar as respostas do formulario de feedback da Kazza.

O projeto usa MySQL para armazenar tanto as respostas do formulario quanto os usuarios de acesso ao painel. A tabela de respostas usada pelo relatorio e a mesma alimentada pelo projeto `feedbackkazza`.

## Funcionalidades

- Login, cadastro e recuperacao de senha com usuarios salvos no MySQL.
- Listagem das respostas da tabela `feedback_workshop`.
- Tabela com busca, ordenacao e paginacao via DataTables.
- Layout administrativo baseado em Bootstrap/AdminLTE.
- Configuracao de banco separada em arquivo local ignorado pelo Git.

## Estrutura

- `index.php`: redireciona para a tela principal.
- `login.php`: tela de login.
- `registrar.php`: tela de cadastro de usuario.
- `recuperar.php`: fluxo de recuperacao de senha.
- `app/index.php`: painel com a tabela de respostas.
- `back/db.php`: conexao MySQL e consulta de feedbacks.
- `back/auth_mysql.php`: funcoes de autenticacao, cadastro e recuperacao.
- `back/config.example.php`: modelo de configuracao do banco.
- `back/migrations/001_create_relatorio_usuarios.sql`: criacao da tabela de usuarios.

## Configuracao

Crie o arquivo local:

```text
back/config.php
```

Use `back/config.example.php` como base e preencha:

```php
<?php

return [
  'DB_HOST' => 'mysql.seudominio.com.br',
  'DB_PORT' => '3306',
  'DB_NAME' => 'nome_do_banco',
  'DB_USER' => 'usuario_do_banco',
  'DB_PASS' => 'senha_do_banco',
  'DB_CHARSET' => 'utf8mb4',
  'APP_BASE_URL' => 'https://seudominio.com.br',
  'MAIL_FROM' => 'no-reply@seudominio.com.br',
  'APP_DEBUG' => false,
];
```

O arquivo `back/config.php` nao deve ser enviado ao Git porque contem credenciais reais.

## Banco De Dados

O relatorio espera a tabela de respostas:

```text
feedback_workshop
```

Campos usados:

- `id`
- `nome`
- `email`
- `telefone`
- `resposta1`
- `resposta2`
- `resposta3`
- `resposta4`
- `evento`
- `receber_novidades`
- `data`

Para os usuarios do painel, execute a migration:

```sql
back/migrations/001_create_relatorio_usuarios.sql
```

Ela cria a tabela:

```text
relatorio_usuarios
```

## Deploy

1. Envie os arquivos do projeto para o servidor PHP.
2. Crie `back/config.php` no servidor com as credenciais reais.
3. Execute a migration de usuarios no MySQL.
4. Acesse `registrar.php` para criar o primeiro usuario.
5. Acesse `login.php` e entre no painel.

## Observacoes

- As senhas sao gravadas com `password_hash()`.
- O login atual nao depende mais do Supabase.
- A recuperacao de senha usa `mail()` do PHP; o envio depende da configuracao de e-mail do servidor.
