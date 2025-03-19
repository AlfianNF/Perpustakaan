<template>
    <div class="min-h-screen bg-gray-100 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Daftar Buku
        </h1>

        <div class="mb-6 flex justify-center">
            <input
                v-model="searchQuery"
                @input="searchBooks"
                type="text"
                placeholder="Cari buku berdasarkan judul,penulis atau kategori..."
                class="px-4 py-2 w-96 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <div class="mb-6 flex justify-center flex-wrap">
            <button
                v-for="category in categories"
                :key="category.id"
                @click="filterByCategory(category.name)"
                class="px-4 py-2 mx-2 my-1 bg-gray-200 rounded-lg hover:bg-gray-300"
            >
                {{ category.name }}
            </button>
        </div>

        <div v-if="loading" class="text-center text-gray-600">Loading...</div>
        <div v-if="error" class="text-red-500 text-center">{{ error }}</div>

        <div
            v-if="!loading && bukuList.length === 0"
            class="text-center text-gray-600"
        >
            Tidak ada buku tersedia.
        </div>

        <div
            v-if="bukuList.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6"
        >
            <div
                v-for="buku in bukuList"
                :key="buku.id"
                class="bg-white p-4 rounded-lg shadow-md cursor-pointer"
                @click="showBook(buku.id)"
            >
                <div class="image-container">
                    <img
                        :src="getImageUrl(buku.image)"
                        alt="Buku"
                        class="book-image"
                    />
                </div>

                <h2 class="text-xl font-semibold text-gray-800">
                    {{ buku.title }}
                </h2>
                <p class="text-gray-600">Penulis: {{ buku.author }}</p>
                <p class="text-gray-600">ISBN: {{ buku.isbn }}</p>
                <p class="text-gray-600">
                    Kategori: {{ buku.category?.name || "Tidak ada kategori" }}
                </p>
                <p class="text-gray-600">
                    Tanggal Terbit: {{ buku.publish_date }}
                </p>
            </div>
        </div>

        <div
            v-if="totalPages > 1"
            class="flex justify-center items-center mt-6 space-x-4"
        >
            <button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 disabled:opacity-50"
            >
                Previous
            </button>
            <span class="text-gray-700"
                >Page {{ currentPage }} of {{ totalPages }}</span
            >
            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 disabled:opacity-50"
            >
                Next
            </button>
        </div>

        <button
            @click="logout"
            class="mt-6 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600"
        >
            Logout
        </button>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Dashboard",
    data() {
        return {
            bukuList: [],
            loading: true,
            error: null,
            baseURL: "http://belajar.test/",
            defaultImage: "https://picsum.photos/230/346",
            currentPage: 1,
            totalPages: 1,
            searchQuery: "",
            categories: [],
            selectedCategoryName: null,
        };
    },
    async created() {
        await Promise.all([this.fetchBooks(), this.fetchCategories()]);
    },
    methods: {
        async fetchBooks() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                let url = `${this.baseURL}api/buku/list?page=${this.currentPage}&search=${this.searchQuery}`;
                if (this.selectedCategoryName) {
                    url += `&category=${this.selectedCategoryName}`;
                }
                const response = await axios.get(url, {
                    headers: { Authorization: `Bearer ${token}` },
                });

                this.bukuList = response.data.data.data;
                this.totalPages = response.data.data.last_page;
            } catch (err) {
                console.error(
                    "Error fetching books:",
                    err.response ? err.response.data : err
                );
                this.error = "Terjadi kesalahan saat mengambil data buku.";
            } finally {
                this.loading = false;
            }
        },

        async fetchCategories() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get(
                    `${this.baseURL}api/category/list`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.categories = response.data.data.data;
            } catch (err) {
                console.error(
                    "Error fetching categories:",
                    err.response ? err.response.data : err
                );
                this.error = "Terjadi kesalahan saat mengambil data kategori.";
            }
        },

        async changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                await this.fetchBooks();
            }
        },

        async searchBooks() {
            this.currentPage = 1;
            this.selectedCategoryName = null;
            await this.fetchBooks();
        },

        async filterByCategory(categoryName) {
            this.currentPage = 1;
            this.selectedCategoryName = categoryName;
            await this.fetchBooks();
        },

        logout() {
            localStorage.removeItem("token");
            window.location.href = "/";
        },

        getImageUrl(imagePath) {
            if (!imagePath) return this.defaultImage;
            if (imagePath.startsWith("http")) return imagePath;
            return `${this.baseURL}storage/${imagePath}`;
        },

        showBook(bookId) {
            this.$router.push({ name: "ShowBuku", params: { id: bookId } });
        },
    },
};
</script>
<style scoped>
    .image-container {
        overflow: hidden; 
    }

    .book-image {
        width: 12rem; 
        height: 17.5rem; 
        object-fit: cover;
        border-radius: 0.5rem;
        margin: 0 auto 0.75rem;
        transition: transform 0.3s ease;
    }

    .book-image:hover {
        transform: scale(1.1);
    }
</style>
