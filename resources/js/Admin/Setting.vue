<template>
  <div class="flex">
    <Sidebar />     
    <div class="ml-64 flex flex-col w-full">
      <Navbar />
      <div class="flex-1 p-4 max-h-screen">
        <h2 class="text-2xl font-semibold mb-4">Edit Profil</h2>
        <form @submit.prevent="updateProfile">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="no_induk">Nomor Induk</label>
            <input
              v-model="profile.no_induk"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="no_induk"
              type="text"
              placeholder="Nomor Induk"
              readonly
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama</label>
            <input
              v-model="profile.name"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="name"
              type="text"
              placeholder="Nama"
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
            <input
              v-model="profile.email"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="email"
              type="email"
              placeholder="Email"
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="image">Gambar Profil</label>
            <input
              type="file"
              @change="handleImageUpload"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
              id="image"
            />
          </div>
          <div v-if="profile.image" class="mb-4">
            <img :src="profile.image" alt="Profile" class="w-32 h-32 rounded-full" />
          </div>
          <button
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            type="submit"
          >
            Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
  name: "Setting",
  components: {
    Sidebar,
    Navbar,
  },
  data() {
    return {
      profile: {
        id: null,
        name: "",
        no_induk: "",
        email: "",
        image: null,
      },
      imageFile: null,
      baseURL: "http://belajar.test/",
    };
  },
  mounted() {
    this.fetchProfile();
  },
  methods: {
    async fetchProfile() {
      try {
        const response = await axios.get("http://belajar.test/api/user", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        this.profile = response.data;
        if (this.profile.image && !this.profile.image.startsWith("http")) {
          this.profile.image = this.baseURL + "storage/" + this.profile.image;
        }
      } catch (error) {
        console.error("Error fetching profile:", error);
      }
    },
    handleImageUpload(event) {
      this.imageFile = event.target.files[0];
      if (this.imageFile) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.profile.image = e.target.result;
        };
        reader.readAsDataURL(this.imageFile);
      }
    },
    async updateProfile() {
      try {
        const formData = new FormData();
        formData.append("_method", "PUT");
        formData.append("name", this.profile.name);
        formData.append("no_induk", this.profile.no_induk);
        formData.append("email", this.profile.email);
        if (this.imageFile) {
          formData.append("image", this.imageFile);
        }

        await axios.post(
          `http://belajar.test/api/user/${this.profile.id}/update`,
          formData,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "multipart/form-data",
            },
          }
        );
        Swal.fire("Berhasil", "Profil berhasil diperbarui", "success");
        this.fetchProfile();
      } catch (error) {
        console.error("Error updating profile:", error);
        Swal.fire("Gagal", "Gagal memperbarui profil", "error");
      }
    },
  },
};
</script>