<template>
    <div class="flex">
      <Sidebar />     
      <div class="ml-64 flex flex-col w-full">
        <Navbar />
        <div class="flex-1 p-4 max-h-screen">
                <h2 class="text-2xl font-semibold mb-4">Daftar Pengguna</h2>

                <div class="mb-4">
                    <input
                        v-model="searchQuery"
                        @input="searchUsers"
                        type="text"
                        placeholder="Cari pengguna..."
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                    />
                </div>

                <div class="mb-4">
                    <select
                        v-model="isAdminFilter"
                        @change="fetchUsers"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option :value="null">Semua</option>
                        <option :value="true">Admin</option>
                        <option :value="false">User</option>
                    </select>
                </div>

                <div v-if="loading" class="text-center text-gray-600">
                    Loading...
                </div>
                <div v-if="error" class="text-red-500 text-center">
                    {{ error }}
                </div>
                <div v-if="users.length > 0" class="overflow-x-auto">
                    <table
                        class="min-w-full bg-white border border-gray-300 text-center"
                    >
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2">No</th>
                                <th class="border border-gray-300 p-2">Nama</th>
                                <th class="border border-gray-300 p-2">
                                    Email
                                </th>
                                <th class="border border-gray-300 p-2">
                                    No Induk
                                </th>
                                <th class="border border-gray-300 p-2">
                                    Is Admin
                                </th>
                                <th class="border border-gray-300 p-2">
                                    Image
                                </th>
                                <th class="border border-gray-300 p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td class="border border-gray-300 p-2">
                                    {{ index + 1 }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ user.name }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ user.email }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ user.no_induk }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ user.is_admin ? "Admin" : "User" }}
                                </td>
                                <td
                                    class="border border-gray-300 p-2 flex justify-center"
                                >
                                    <img
                                        v-if="user.image"
                                        :src="getImageUrl(user.image)"
                                        alt="User Image"
                                        class="w-16 h-16 object-cover rounded-full"
                                    />
                                    <img
                                        v-else
                                        src="https://images.pexels.com/photos/1704488/pexels-photo-1704488.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500"
                                        alt="Default User Image"
                                        class="w-16 h-16 object-cover rounded-full"
                                    />
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <button
                                        @click="editUser(user.id)"
                                        class="px-3 py-1 bg-blue-500 text-white rounded-md mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteUser(user.id)"
                                        class="px-3 py-1 bg-red-500 text-white rounded-md"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-else-if="!loading && !error"
                    class="text-center text-gray-600"
                >
                    Tidak ada pengguna yang tersedia.
                </div>
                <div
                    v-if="pagination.last_page > 1"
                    class="flex justify-center mt-4"
                >
                    <button
                        :disabled="pagination.current_page === 1"
                        @click="changePage(pagination.current_page - 1)"
                        class="px-3 py-1 mx-1 border rounded-md"
                    >
                        Previous
                    </button>
                    <span class="px-3 py-1 mx-1">
                        Page {{ pagination.current_page }} of
                        {{ pagination.last_page }}
                    </span>
                    <button
                        :disabled="
                            pagination.current_page === pagination.last_page
                        "
                        @click="changePage(pagination.current_page + 1)"
                        class="px-3 py-1 mx-1 border rounded-md"
                    >
                        Next
                    </button>
                </div>
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
    name: "User",
    components: {
        Sidebar,
        Navbar,
    },
    data() {
        return {
            users: [],
            loading: true,
            error: null,
            baseURL: "http://belajar.test/",
            searchQuery: "",
            isAdminFilter: null,
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
        };
    },
    async created() {
        await this.fetchUsers();
    },
    methods: {
        async fetchUsers() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                let url = `${this.baseURL}api/user/list`;
                const params = {
                    page: this.pagination.current_page,
                };
                if (this.searchQuery) {
                    params.search = this.searchQuery;
                }
                if (this.isAdminFilter !== null) {
                    params.is_admin = this.isAdminFilter;
                }
                const response = await axios.get(url, {
                    headers: { Authorization: `Bearer ${token}` },
                    params: params,
                });
                this.users = response.data.data.data;
                this.pagination = {
                    current_page: response.data.data.current_page,
                    last_page: response.data.data.last_page,
                    total: response.data.data.total,
                };
            } catch (err) {
                console.error(
                    "Error fetching users:",
                    err.response ? err.response.data : err
                );
                this.error = "Terjadi kesalahan saat mengambil data pengguna.";
            } finally {
                this.loading = false;
            }
        },
        async searchUsers() {
            await this.fetchUsers();
        },
        changePage(page) {
            this.pagination.current_page = page;
            this.fetchUsers();
        },
        getImageUrl(imagePath) {
            if (!imagePath) return this.defaultImage;
            if (imagePath.startsWith("http")) return imagePath;
            return `${this.baseURL}storage/${imagePath}`;
        },
        editUser(userId) {
            this.$router.push({
                path: `/dashboard-admin/users/${userId}/edit`,
            });
        },
        async deleteUser(userId) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Anda tidak akan dapat mengembalikan pengguna ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = localStorage.getItem("token");
                        await axios.delete(
                            `${this.baseURL}api/user/${userId}/delete`,
                            {
                                headers: { Authorization: `Bearer ${token}` },
                            }
                        );

                        this.fetchUsers();

                        Swal.fire(
                            "Terhapus!",
                            "Pengguna telah dihapus.",
                            "success"
                        );
                    } catch (err) {
                        console.error(
                            "Error deleting user:",
                            err.response ? err.response.data : err
                        );

                        Swal.fire(
                            "Gagal!",
                            "Gagal menghapus pengguna.",
                            "error"
                        );
                    }
                }
            });
        },
    },
};
</script>
