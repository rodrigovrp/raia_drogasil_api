# Projeto

Seja bem vindo ao projeto da IPDV

## 1 - Requisitos

  - PHP ^7.2.5
  - Composer
  - PostgreSQL 9 ou Superior

## 2 - Instalação

###  2.1 - Dependências
Instalação das dependências do projeto.

```sh
$ composer install
```

###  2.2 - Variáveis de Ambiente

Copiar arquivo .env.example para .env na raiz do projeto
```sh
$ cp .env.example .env
```

Configuração das variáveis de ambiente

 - APP_NAME="Nome Do Projeto"

Configurações de Banco de dados

Porta padrão do postgresql é **5432**

 - DB_CONNECTION=pgsql
 - DB_HOST=127.0.0.1
 - DB_PORT=3306
 - DB_DATABASE=nome_do_banco_dedos
 - DB_USERNAME=root
 - DB_PASSWORD=

Na raiz do projeto execute os seguintes comandos:

```sh
$ php artisan optimize
```

```sh
$ php artisan key:generate
```

###  2.3 - Fazendo migration das tabelas para o banco de dados

```sh
$ php artisan migrate
```

###  2.4 - Inicialização

Inicializa o sistema no localhost:8080 (padrão)
```sh
$ php artisan serve
```
#   r a i a _ d r o g a s i l _ a p i  
 #   r a i a _ d r o g a s i l _ a p i  
 