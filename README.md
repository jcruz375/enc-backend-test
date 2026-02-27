# ENC Backend Test - Product Importer

> **Nota:** Este projeto utilizou IA para documentação, auxílio em métodos de consulta ao banco e padronização em DTOs.

> **Insomnia:** Há um arquivo de exportação na raiz do projeto. Basta importá-lo no Insomnia para ter acesso aos testes de requisição à API. `Insomnia_2026-02-27.yaml`

Este projeto é uma aplicação Laravel containerizada focada na importação e sincronização de produtos de uma API externa (FakeStoreAPI).

## 🚀 Tecnologias Utilizadas

- **PHP 8.4** (Laravel 12)
- **Docker & Docker Compose** (Nginx, PHP-FPM, MySQL, Redis)
- **MySQL 8.0**

## 🛠️ Instalação e Configuração

Siga os passos abaixo para rodar o projeto localmente usando Docker.

### 1. Configuração Inicial

Clone o repositório e configure as variáveis de ambiente:

```bash
cp .env.example .env
```

### 2. Subir o Ambiente

Construa e inicie os containers:

```bash
docker-compose up -d --build
```

### 3. Instalação de Dependências

Instale as dependências do PHP e gere a chave da aplicação:

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
```

### 4. Banco de Dados

Execute as migrações para criar as tabelas necessárias (`products`, `jobs`, etc):

```bash
docker-compose exec app php artisan migrate
```

A aplicação estará disponível em: `http://localhost:8080`

---

## 📦 Funcionalidade: Importação de Produtos

O sistema possui um comando customizado para buscar produtos da FakeStoreAPI e salvá-los no banco de dados. O processo evita duplicatas atualizando registros existentes baseados no ID externo.

### Como Executar

Para disparar a importação manualmente via terminal:

```bash
docker-compose exec app php artisan products:import
```

### Arquitetura da Solução

- **Service (`FakeStoreService`)**: Responsável isoladamente pela comunicação HTTP com a API externa.
- **Job (`ImportProductsJob`)**: Processa os dados em background. Utiliza `updateOrCreate` para garantir a idempotência (cria se não existir, atualiza se existir).
- **Command (`ImportProductsCommand`)**: Interface de linha de comando para facilitar o disparo do Job.

> **Nota sobre Filas:** Se a variável `QUEUE_CONNECTION` no `.env` estiver definida como `sync`, a importação ocorrerá imediatamente. Se estiver como `database` ou `redis`, será necessário rodar o worker: `docker-compose exec app php artisan queue:work`.

---

## 📖 Documentação da API

A aplicação expõe os seguintes endpoints RESTful (prefixo `/api`):

### 1. Listagem de Produtos

Retorna uma lista paginada de produtos.

**GET** `/api/products`

| Parâmetro | Descrição | Exemplo |
|-----------|-----------|---------|
| `page` | Número da página | `1` |
| `search` | Busca parcial no título | `shirt` |
| `category` | Filtro exato de categoria | `men's clothing` |
| `price_min` | Preço mínimo | `10` |
| `price_max` | Preço máximo | `100` |
| `rating_min` | Avaliação mínima (0-5) | `4` |
| `sort_by` | Ordenação (`price`, `title`, `rating_rate`) | `price` |
| `order` | Direção (`asc`, `desc`) | `desc` |

```bash
curl -X GET "http://localhost:8080/api/products?category=electronics&sort_by=price&order=desc"
```

### 2. Estatísticas

Retorna métricas consolidadas do catálogo (total, média de preço, contagem por categoria, etc).

**GET** `/api/products/stats`

### 3. Detalhes do Produto

Exibe dados de um produto específico, incluindo preço com taxa calculada.

**GET** `/api/products/{id}`

### 4. Atualização Parcial

Atualiza título, preço ou categoria. Gera log de alterações interno.

**PATCH** `/api/products/{id}`

```json
{
  "price": 29.99,
  "title": "Novo Título"
}
```

### 5. Exclusão (Soft Delete)

Remove um produto. **Nota:** Produtos com avaliação superior a 4.5 não podem ser excluídos.

**DELETE** `/api/products/{id}`
