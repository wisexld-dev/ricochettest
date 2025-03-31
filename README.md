## Descrição
Este projeto é uma aplicação web desenvolvida como um teste técnico. A aplicação permite a autenticação de usuários (registro e login), o cadastro de clientes e o agendamento de reuniões. Além disso, conta com um sistema de notificações que lembra os usuários sobre reuniões próximas.

## Funcionalidades
- **Autenticação de Usuários**: Permite que os usuários se registrem e façam login na aplicação.
- **Cadastro de Clientes**: Usuários autenticados podem gerenciar informações de clientes.
- **Agendamento de Reuniões**: Os usuários podem agendar reuniões, que são armazenadas no banco de dados.
- **Sistema de Notificações**: Notificações são enviadas para lembrar os usuários sobre reuniões futuras.

## Estrutura do Projeto

### Backend
O backend é construído com PHP e Laravel. A estrutura do diretório é a seguinte:
- `app/`: Contém a lógica da aplicação e os controladores.
- `routes/`: Define as rotas da API.
- `database/`: Contém as migrações e seeders.

### Frontend
O frontend é construído com Vue.js e Vite. A estrutura do diretório é a seguinte:
- `src/`: Contém os componentes Vue e a lógica da aplicação.
- `layouts/`: Contém o layout basico da tela incluindo a navbar.
- `pages/`: Contém os componentes para as telas de clientes, meetings e dashboard.
- `pages/Auth`: Contém os componentes responsáveis pelo login/register.

## Instalação

### Pré-requisitos
- PHP
- Composer
- Node.js
- npm

### Clonando o Repositório
```bash
git clone https://github.com/wisexld-dev/ricochettest.git
cd <diretorio-do-repositorio>
```

### Configuração
1. Navegue até o diretório do backend:
   ```bash
   cd backend
   ```
2. Instale as dependências do PHP:
   ```bash
   composer install
   ```

3. Configure as variáveis de ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Navegue até o diretório do frontend:
   ```bash
   cd ../frontend
   ```
5. Instale as dependências do Node.js:
   ```bash
   npm install
   ```
6. Montar e subir container MySQL e phpMyAdmin (opcional)
```bash
   cd /docker
   docker-compose up -d
```

## Como Utilizar o Projeto
- Para iniciar o backend, execute:
   ```bash
   php artisan serve
   ```

- Para iniciar o frontend, execute:
   ```bash
   npm run dev
   ```

Tanto o frontend quanto o backend estarão disponíveis na url indicada no console.

## Arquitetura do Projeto
1. **Autenticação JWT**: O sistema utiliza JSON Web Tokens (JWT) para autenticação de usuários. Após o login, um token é gerado e deve ser enviado em cada requisição subsequente para acessar rotas protegidas.

2. **Backend como API**: O backend é configurado para funcionar como uma API, fornecendo endpoints para operações de CRUD (Create, Read, Update, Delete) em clientes e reuniões.

3. **Docker para MySQL e phpMyAdmin**: O projeto utiliza um Dockerfile para configurar um ambiente com MySQL e phpMyAdmin, facilitando a gestão do banco de dados.

4. **Vue.js para SPA**: O frontend é desenvolvido com Vue.js, permitindo a criação de uma Single Page Application (SPA) que se comunica com o backend via API.

## Testes Unitários
Os testes unitários estão localizados no diretório `backend/tests/`. Eles são utilizados para garantir que a lógica da aplicação funcione conforme o esperado.
- `Unit/`: Contém testes unitários que verificam a lógica de classes e métodos individuais.

Para executar os testes, você pode usar o seguinte comando no diretório do backend:
```bash
php artisan test
```

## Scheduler
Para executar o envio de lembretes de reunião utilize:
```bash
php artisan meetings:send-reminders
```
