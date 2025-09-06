# Sistema de Gerenciamento Escolar

Sistema de gerenciamento escolar desenvolvido em Laravel com API RESTful para gerenciar cursos, disciplinas, alunos e matrículas.

## Tecnologias

-   **PHP 8.4** - Linguagem
-   **Laravel 12** - Framework
-   **SQLite** - Banco de dados
-   **Swagger/OpenAPI** - Documentação da API
-   **Docker** - Containerização
-   **Mailpit** - Servidor de e-mail para testes

## Pré-requisitos

-   Docker e Docker Compose

## Instalação e Execução

### Execução com Docker

1. **Clone o repositório**

    ```bash
    git clone
    cd teste-bussola
    ```

2. **Execute com Docker Compose**

    ```bash
    docker-compose up --build
    ```

3. **Acesse a aplicação**
    - **API**: http://localhost:8000
    - **Documentação Swagger**: http://localhost:8000/api/documentation
    - **Mailpit (E-mails)**: http://localhost:8025

## Documentação da API

### Collection do Postman

Para facilitar os testes da API, foi criada uma collection completa do Postman:

**Arquivo**: `Bussola_API_Collection.postman_collection.json`

## Testes

Execute os testes automatizados:

```bash
docker-compose exec app php artisan test
```

## Configuração de E-mail

O sistema está configurado para usar o Mailpit para testes de e-mail:

-   **Web UI**: http://localhost:8025

## 🐳 Comandos Docker Úteis

```bash
# Iniciar os containers
docker-compose up -d

# Parar os containers
docker-compose down

# Ver logs em tempo real
docker-compose logs -f app

# Executar comandos no container
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan test

# Rebuild dos containers
docker-compose up --build

# Limpar volumes
docker-compose down -v
```

## Regras de Negócio

1. **Cursos**: Não podem ser excluídos se tiverem matrículas ativas
2. **Alunos**: Devem estar vinculados a pelo menos um curso ao serem criados
3. **Matrículas**: Não podem ser duplicadas (mesmo aluno + mesmo curso)
4. **E-mail**: Enviado automaticamente ao realizar matrícula
5. **Disciplinas**: Podem pertencer a múltiplos cursos

## Monitoramento

### Logs

-   **Com Docker**: `docker-compose logs app`
-   **Arquivo local**: `storage/logs/laravel.log`

### Banco de Dados

-   **Tipo**: SQLite
-   **Arquivo**: `database/database.sqlite`
-   **Acessível via**: DBeaver, ou qualquer cliente SQLite

## 👨‍💻 Autor

Desenvolvido por Kelvyn Krug como parte do processo seletivo.
