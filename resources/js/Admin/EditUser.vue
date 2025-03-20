<template>
    <div class="flex">
        <Sidebar />
        <div class="flex-1 flex flex-col">
            <Navbar />
            <div class="flex-1 overflow-x-hidden overflow-y-auto p-4">
                <h2 class="text-2xl font-semibold mb-4">Edit Pengguna</h2>
                <div v-if="loading" class="text-center text-gray-600">
                    Loading...
                </div>
                <div v-if="error" class="text-red-500 text-center">
                    {{ error }}
                </div>
                <div v-if="user">
                    <form @submit.prevent="updateUser">
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="name"
                            >
                                Nama
                            </label>
                            <input
                                v-model="user.name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="name"
                                type="text"
                                placeholder="Nama"
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="email"
                            >
                                Email
                            </label>
                            <input
                                v-model="user.email"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="email"
                                type="email"
                                placeholder="Email"
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="no_induk"
                            >
                                No Induk
                            </label>
                            <input
                                v-model="user.no_induk"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="no_induk"
                                type="text"
                                placeholder="No Induk"
                                readonly
                            />
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="is_admin"
                            >
                                Is Admin
                            </label>
                            <select
                                v-model="user.is_admin"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="is_admin"
                            >
                                <option :value="true">Admin</option>
                                <option :value="false">User</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label
                                class="block text-gray-700 text-sm font-bold mb-2"
                                for="image"
                            >
                                Gambar
                            </label>
                            <input
                                type="file"
                                @change="onFileChange"
                                accept="image/*"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="image"
                            />
                            <div v-if="previewImage" class="mt-2">
                                <img
                                    :src="previewImage"
                                    alt="Preview Gambar"
                                    class="w-32 h-32 object-cover rounded-full"
                                />
                            </div>
                            <div v-else-if="user.image" class="mt-2">
                                <img
                                    :src="getImageUrl(user.image)"
                                    alt="Gambar Saat Ini"
                                    class="w-32 h-32 object-cover rounded-full"
                                />
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <button
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Sidebar from '@/components/Sidebar.vue';
  import Navbar from '@/components/Navbar.vue';
  import axios from "axios";

  export default {
    name: "UserEdit",
    components: {
      Sidebar,
      Navbar,
    },
    data() {
      return {
        user: null,
        loading: true,
        error: null,
        baseURL: "http://belajar.test/",
        previewImage: null,
        selectedFile: null,
      };
    },
    async created() {
      await this.fetchUser();
    },
    methods: {
      async fetchUser() {
        this.loading = true;
        try {
          const token = localStorage.getItem("token");
          const response = await axios.get(
            `${this.baseURL}api/user/${this.$route.params.id}/show`,
            {
              headers: { Authorization: `Bearer ${token}` },
            }
          );
          this.user = response.data;
          console.log("User fetched:", this.user);
        } catch (err) {
          console.error("Error fetching user:", err);
          this.error = "Gagal mengambil data pengguna.";
        } finally {
          this.loading = false;
        }
      },
      async updateUser() {
        try {
            const token = localStorage.getItem("token");
            const formData = new FormData();

            formData.append("_method", "PUT");

            formData.append("name", this.user.name);
            formData.append("email", this.user.email);
            formData.append("no_induk", this.user.no_induk);
            formData.append("is_admin", this.user.is_admin);

            if (this.selectedFile) {
            formData.append("image", this.selectedFile);
            }

            await axios.post(
            `${this.baseURL}api/user/${this.$route.params.id}/update`, 
            formData,
            {
                headers: {
                Authorization: `Bearer ${token}`,
                "Content-Type": "multipart/form-data",
                },
            }
            );

            this.$router.push("/dashboard-admin/users");
        } catch (err) {
            console.error("Error updating user:", err);
            this.error = "Gagal memperbarui data pengguna.";
        }
     },

      onFileChange(event) {
        this.selectedFile = event.target.files[0];
        if (this.selectedFile) {
          this.previewImage = URL.createObjectURL(this.selectedFile);
        } else {
          this.previewImage = null;
        }
      },
      getImageUrl(imagePath) {
        if (!imagePath) return "https://images.pexels.com/photos/1704488/pexels-photo-1704488.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500";
        if (imagePath.startsWith("http")) return imagePath;
        return `${this.baseURL}storage/${imagePath}`;
      },
    },
  };
</script>
