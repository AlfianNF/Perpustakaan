<template>
    <div class="flex">
      <Sidebar />
      <div class="flex-1 flex flex-col">
        <Navbar />
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
          Kembali
        </div>
      </div>
    </div>
</template>
  
<script>
  import Sidebar from '@/components/Sidebar.vue';
  import Navbar from '@/components/Navbar.vue';
  import DashboardContent from '@/components/DashboardContent.vue';
  
  export default {
    name: 'Kembali',
    components: {
      Sidebar,
      Navbar,
      DashboardContent,
    },
  };
</script>