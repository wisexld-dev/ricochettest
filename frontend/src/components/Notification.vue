<template>
    <div v-if="visible" class="notification" :class="type">
      <p>{{ message }}</p>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      message: {
        type: String,
        required: true,
      },
      type: {
        type: String,
        default: "info",
      },
    },
    data() {
      return {
        visible: true,
      };
    },
    watch: {
      message() {
        this.visible = true;
        setTimeout(() => {
          this.visible = false;
        }, 5000);
      },
    },
  };
  </script>
  
  <style scoped>
  .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: #fff;
    z-index: 1000;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }
  .notification.info {
    background-color: #007bff;
  }
  .notification.success {
    background-color: #28a745;
  }
  .notification.error {
    background-color: #dc3545;
  }
  </style>