<template>
    <div class="flex">
        <Sidebar />
        <div class="ml-64 flex flex-col w-full">
            <Navbar />
            <div
                class="flex-1 p-4 max-h-screen"
            >
                <h2 class="text-2xl font-semibold mb-4">Daftar Buku</h2>
                <div class="mb-4">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <button
                            @click="showModal = true"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        >
                            Tambah Buku
                        </button>
                        <select
                            v-model="filterCategory"
                            @change="fetchBuku(1)"
                            class="border p-2 rounded w-full md:w-1/4"
                        >
                            <option value="">Semua Kategori</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.name"
                            >
                                {{ cat.name }}
                            </option>
                        </select>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari buku..."
                            class="border p-2 rounded w-full md:w-1/3"
                            @input="fetchBuku(1)"
                        />
                    </div>

                </div>

                <div v-if="loading" class="text-center text-gray-600">
                    Memuat data buku...
                </div>
                <div v-if="error" class="text-red-500 text-center">
                    Terjadi kesalahan: {{ error }}
                </div>

                <div v-if="daftarBuku.length > 0" class="overflow-x-auto">
                    <table
                        class="min-w-full bg-white border border-gray-300 text-center"
                    >
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2">No</th>
                                <th class="border border-gray-300 p-2">
                                    Judul
                                </th>
                                <th class="border border-gray-300 p-2">
                                    Penulis
                                </th>
                                <th class="border border-gray-300 p-2">ISBN</th>
                                <th class="border border-gray-300 p-2">
                                    Kategori
                                </th>
                                <th class="border border-gray-300 p-2">
                                    Tanggal Terbit
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(buku, index) in daftarBuku"
                                :key="buku.id"
                            >
                                <td class="border border-gray-300 p-2">
                                    {{ index + 1 }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ buku.title }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ buku.author }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ buku.isbn }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ buku.category?.name || "-" }}
                                </td>
                                <td class="border border-gray-300 p-2">
                                    {{ formatDate(buku.publish_date) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-else-if="!loading && !error"
                    class="text-center text-gray-600"
                >
                    Tidak ada data buku yang tersedia.
                </div>
                <!-- Pagination -->
                <div
                    v-if="pagination.last_page > 1"
                    class="flex justify-center mt-4 gap-2"
                >
                    <button
                        :disabled="pagination.current_page === 1"
                        @click="fetchBuku(pagination.current_page - 1)"
                        class="px-4 py-2 border rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                    >
                        Previous
                    </button>
                    <span class="px-4 py-2"
                        >Halaman {{ pagination.current_page }} dari
                        {{ pagination.last_page }}</span
                    >
                    <button
                        :disabled="
                            pagination.current_page === pagination.last_page
                        "
                        @click="fetchBuku(pagination.current_page + 1)"
                        class="px-4 py-2 border rounded-md bg-gray-200 hover:bg-gray-300 disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Buku -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        >
            <div class="bg-white rounded-lg w-full max-w-lg p-6 relative">
                <h3 class="text-xl font-semibold mb-4">Tambah Buku</h3>

                <form @submit.prevent="submitBuku">
                    <div class="mb-2">
                        <label class="block mb-1">Judul</label>
                        <input
                            v-model="form.title"
                            type="text"
                            class="w-full border p-2 rounded"
                            required
                        />
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Penulis</label>
                        <input
                            v-model="form.author"
                            type="text"
                            class="w-full border p-2 rounded"
                            required
                        />
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">ISBN</label>
                        <input
                            v-model="form.isbn"
                            type="text"
                            class="w-full border p-2 rounded"
                            required
                        />
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Tanggal Terbit</label>
                        <input
                            v-model="form.publish_date"
                            type="date"
                            class="w-full border p-2 rounded"
                            required
                        />
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Kategori</label>
                        <select
                            v-model="form.category"
                            class="w-full border p-2 rounded"
                            required
                            >
                            <option disabled value="">-- Pilih Kategori --</option>
                            <option
                                v-for="cat in categories"
                                :key="cat.id"
                                :value="cat.id"
                            >
                                {{ cat.name }}
                            </option>
                        </select>

                    </div>

                    <div class="mb-4">
                        <label class="block mb-1">Gambar</label>
                        <input
                            type="file"
                            @change="handleImageUpload"
                            class="w-full border p-2 rounded"
                        />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 border rounded hover:bg-gray-200"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import { format } from "date-fns";
import { id } from "date-fns/locale";
import axios from "axios";

export default {
    name: "Buku",
    components: {
        Sidebar,
        Navbar,
    },
    data() {
        return {
            daftarBuku: [],
            showModal: false,
            form: {
                title: "",
                author: "",
                isbn: "",
                publish_date: "",
                category: "",
                image: null,
            },
            categories: [],
            loading: false,
            error: null,
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 10,
            },
            searchQuery: "",  
            filterCategory: "", 
        };
    },
    mounted() {
        this.fetchBuku();
        this.fetchCategories();
        this.fetchBuku(1);
    },
    methods: {
        formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            return format(date, "d MMMM yyyy", { locale: id });
        },
        async fetchBuku(page = 1) {
    this.loading = true;
    this.error = null;
    const token = localStorage.getItem("token");

    const params = new URLSearchParams();
    params.append("page", page);
    if (this.searchQuery) params.append("search", this.searchQuery);
    if (this.filterCategory) params.append("category", this.filterCategory);

    try {
        const response = await axios.get(
            `http://belajar.test/api/buku/list?${params.toString()}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );

        this.daftarBuku = response.data.data.data;
        this.pagination.current_page = response.data.data.current_page;
        this.pagination.last_page = response.data.data.last_page;
        this.pagination.per_page = response.data.data.per_page;
    } catch (err) {
        this.error = err.message;
    } finally {
        this.loading = false;
    }
        },

        handleImageUpload(event) {
            this.form.image = event.target.files[0];
        },

        async fetchCategories() {
            const token = localStorage.getItem("token");
            try {
                const response = await axios.get(
                    "http://belajar.test/api/category/list",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.categories = response.data.data.data;

            } catch (error) {
                console.error("Gagal mengambil kategori:", error);
            }
        },

        async submitBuku() {
            const token = localStorage.getItem("token");
            const formData = new FormData();
            formData.append("title", this.form.title);
            formData.append("author", this.form.author);
            formData.append("isbn", this.form.isbn);
            formData.append("publish_date", this.form.publish_date);
            formData.append("category", this.form.category);
            if (this.form.image) {
                formData.append("image", this.form.image);
            }

            try {
                await axios.post(
                    "http://belajar.test/api/buku/create",
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );
                this.showModal = false;
                this.resetForm();
                this.fetchBuku();
            } catch (error) {
                console.error("Gagal menambahkan buku:", error);
            }
        },

        resetForm() {
            this.form = {
                title: "",
                author: "",
                isbn: "",
                publish_date: "",
                category: "",
                image: null,
            };
        },
    },
};
</script>
