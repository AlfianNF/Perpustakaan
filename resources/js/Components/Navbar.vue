<template>
    <nav class="bg-gray-900 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-lg font-semibold">Admin Dashboard</h1>
        <button @click="logout" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Logout</button>
      </div>
    </nav>
</template>
  
<script>
  export default {
    name: 'Navbar',
    methods: {
      logout() {
        localStorage.removeItem('token');
        this.$router.push('/login'); // Arahkan ke halaman login
      },
    },
  };
</script>