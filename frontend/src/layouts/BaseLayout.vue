<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
      <div class="container">
        <RouterLink class="navbar-brand" to="/dashboard">Ricochet360</RouterLink>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/dashboard">Dashboard</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/clients">Clientes</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/schedule">Reuniões</RouterLink>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <slot></slot>
  </div>
</template>