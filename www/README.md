<div align="center">
    <h1>🏫 SGE - School Management Enterprise SaaS</h1>
    <p>Uma plataforma Multi-Tenant completa para gestão pedagógica, financeira e administrativa de escolas e franquias educacionais.</p>
</div>

---

## 🚀 Sobre o Projeto

O **SGE (Sistema de Gestão Escolar)** é uma aplicação SaaS desenvolvida em **Laravel 11** projetada para escalar. De uma escola local a uma rede de franquias educacionais, o sistema utiliza o conceito de _Multi-Tenancy_ guiado por Sessão (Context Switcher) para isolar dados, garantindo máxima segurança (ACL Role-Based) e performance.

### 🌟 Principais Módulos

- 🔐 **Role-Based Access Control (RBAC):** Portais isolados para cada tipo de usuário (Professor não vê o financeiro; Aluno só vê o próprio boletim; Admin enxerga a visão global).
- 🏢 **Multi-Tenancy (Franquias):** Cadastre múltiplas unidades escolares. Os administradores podem alternar entre unidades usando o _Context Switcher_ na barra superior.
- 🎯 **Captação (CRM de Leads):** Landing Page pública integrada. Pais solicitam pré-matrículas pelo site, e a Secretaria converte o "Lead" em "Aluno" com 1 clique.
- 🧠 **Automação Pedagógica (IA):**
    - _Assistente de Enturmação:_ Algoritmo matemático _Round-Robin_ que pega uma fila de alunos recém-matriculados e os divide homogeneamente entre as turmas existentes, respeitando o limite físico (capacity) das salas.
- 📝 **Diário de Classe do Professor:** Interface focada em produtividade. O professor faz a Chamada (Frequência) de todos os alunos em lote, e lança notas vinculadas a avaliações cadastradas.
- 💰 **Módulo Financeiro:** Faturamento, gerenciamento de Mensalidades, baixa de boletos e multas baseadas nas configurações globais da Unidade.
- ⚙️ **Configurações Globais:** Personalize o comportamento de cada escola independentemente (Ex: "Frequência Mínima", "Máximo de Alunos por Turma").

## 🛠️ Stack Tecnológica

- **Backend:** PHP 8.3 / Laravel 11
- **Frontend:** Bootstrap 5.3 (Glassmorphism UI), Blade Components, DataTables (Server-Side)
- **Database:** MariaDB (Containers Docker)
- **Assets:** Vite
- **Permissões:** Padrão ACL (Baseado em Roles e Middleware)

## 📦 Como Rodar o Projeto

### Pré-requisitos

- Docker & Docker Compose

### Passos da Instalação

1.  Clone o repositório:
    ```bash
    git clone https://github.com/alessandroisdev/school-manager.git
    cd school-manager/www
    ```
2.  Copie o arquivo de ambiente:
    ```bash
    cp .env.example .env
    ```
3.  Inicie os containers:
    ```bash
    docker-compose up -d
    ```
4.  Acesse o container da aplicação e instale as dependências:
    ```bash
    docker exec -it school_manager_app bash
    composer install
    npm install
    npm run build
    ```
5.  Gere a chave da aplicação e rode as migrações (com os Seeders):
    ```bash
    php artisan key:generate
    php artisan migrate:fresh --seed
    ```

### Credenciais de Teste (Pós-Seed)

O sistema criará o ambiente base inicial:

- **URL:** `http://localhost:8000`
- **Admin Login:** `admin@escola.com` | **Senha:** `password`

## 📖 Documentação da API

O projeto inclui a especificação das rotas de integração e automação na raiz do repositório no formato OpenAPI 3.0.
Para visualizar os endpoints documentados, importe o arquivo `openapi.yaml` no **Swagger Editor** ou no **Postman**.

---

_Desenvolvido por AlessandroIsDev._
