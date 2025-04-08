<template>
    <div class="flex">
      <Sidebar />     
      <div class="ml-64 flex flex-col w-full">
        <Navbar />
        <div class="flex-1 p-4 max-h-screen">
                <h2 class="text-2xl font-semibold mb-4">Daftar Pengembalian</h2>
                <div class="mb-4 flex gap-4 items-center">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari buku atau user..."
                        class="p-2 border rounded w-1/3"
                        @keyup.enter="fetchKembaliList"
                    />
                    <button
                        @click="fetchKembaliList"
                        class="px-4 py-2 bg-blue-500 text-white rounded inline-flex items-center gap-2"
                    >
                        Cari
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.3-4.3"></path>
                        </svg>
                    </button>
                    <button
                        @click="showModal = true"
                        class="px-4 py-2 bg-green-500 text-white rounded inline-flex items-center gap-2"
                    >
                        Tambah
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path d="M5 12h14M12 5v14"></path>
                        </svg>
                    </button>
                </div>
                <div v-if="loading" class="text-center">Loading...</div>
                <div v-else-if="kembaliList.length">
                    <div class="bg-white shadow rounded-md p-4">
                        <table
                            class="min-w-full bg-white border border-gray-200 text-center"
                        >
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">
                                        No
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Nama Peminjam
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Judul Buku
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Tanggal Kembali
                                    </th>
                                    <th class="border border-gray-300 p-2">
                                        Denda
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in kembaliList"
                                    :key="item.id"
                                >
                                    <td class="border border-gray-300 p-2">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{
                                            item.peminjaman?.user_pinjam
                                                ?.name || "-"
                                        }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{
                                            item.peminjaman?.buku?.title || "-"
                                        }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        {{ formatDate(item.tgl_kembali) }}
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        Rp. {{ item.denda }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-between items-center mt-4">
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
                            >
                                Previous
                            </button>

                            <span
                                >Halaman {{ currentPage }} dari
                                {{ totalPages }}</span
                            >

                            <button
                                @click="nextPage"
                                :disabled="currentPage >= totalPages"
                                class="px-3 py-1 bg-gray-300 rounded disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else>Tidak ada data pengembalian.</div>
            </div>
        </div>

        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div class="bg-white p-6 rounded shadow-lg w-1/3">
                <h2 class="text-xl font-semibold mb-4">Tambah Pengembalian</h2>

                <select
                    v-if="pinjamList.length"
                    v-model="selectedPinjam"
                    @change="fillForm"
                    class="w-full p-2 border rounded mb-2"
                >
                    <option
                        v-for="pinjam in pinjamList"
                        :key="pinjam.id"
                        :value="pinjam"
                    >
                        {{ pinjam.user_pinjam?.name || "Tidak Ada Nama" }} -
                        {{ pinjam.buku?.title || "Tidak Ada Judul" }}
                    </option>
                </select>

                <input type="hidden" v-model="newKembali.id_pinjam" />
                <input type="hidden" v-model="newKembali.id_user" />
                <input type="hidden" v-model="newKembali.id_buku" />

                <input
                    v-model="newKembali.tgl_kembali"
                    type="date"
                    class="w-full p-2 border rounded mb-2"
                />

                <div class="flex justify-end gap-2">
                    <button
                        @click="showModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded"
                    >
                        Batal
                    </button>
                    <button
                        @click="addKembali"
                        class="px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        Simpan
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
    name: "Kembali",
    components: { Sidebar, Navbar },
    data() {
        return {
            kembaliList: [],
            pinjamList: [],
            loading: false,
            searchQuery: "",
            currentPage: 1,
            totalPages: 1,
            showModal: false,
            newKembali: {
                id_pinjam: "",
                id_user: "",
                id_buku: "",
                tgl_kembali: "",
            },
            selectedPinjam: null,
        };
    },
    mounted() {
        this.fetchKembaliList();
        this.fetchPinjamList();
    },
    methods: {
        async fetchKembaliList() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");

                const params = {
                    page: this.currentPage,
                };

                if (this.searchQuery) {
                    params.search = this.searchQuery;
                }

                const response = await axios.get(
                    "http://belajar.test/api/kembali/list",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                        params,
                    }
                );

                this.kembaliList = response.data.data.data || [];
                this.totalPages = response.data.data.last_page || 1;
            } catch (err) {
                console.error("Error fetching kembali list:", err);
            } finally {
                this.loading = false;
            }
        },
        async fetchPinjamList() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get(
                    "http://belajar.test/api/pinjam/list?status=dipinjam",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.pinjamList = response.data.data.data || [];
            } catch (err) {
                console.error("Error fetching pinjam list:", err);
            }
        },
        fillForm() {
            if (this.selectedPinjam) {
                this.newKembali.id_pinjam = this.selectedPinjam.id;
                this.newKembali.id_user = this.selectedPinjam.id_user;
                this.newKembali.id_buku = this.selectedPinjam.id_buku;
            }
        },

        async addKembali() {
            try {
                const token = localStorage.getItem("token");

                const payload = {
                    id_pinjam: this.newKembali.id_pinjam,
                    id_user: this.newKembali.id_user,
                    id_buku: this.newKembali.id_buku,
                    tgl_kembali: this.newKembali.tgl_kembali,
                };

                const response = await axios.post(
                    "http://belajar.test/api/kembali/create",
                    payload,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                console.log("Pengembalian berhasil:", response.data);
                this.showModal = false;
                this.fetchPinjamList();

                Swal.fire(
                    "Berhasil!",
                    "Data pengembalian berhasil ditambahkan.",
                    "success"
                );
            } catch (err) {
                Swal.fire(
                    "Gagal!",
                    "Gagal menambahkan data pengembalian.",
                    "error"
                );
            }
        },
        formatDate(dateString) {
            if (!dateString) return "-";
            const date = new Date(dateString);
            return date.toLocaleDateString("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
            });
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.fetchKembaliList();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.fetchKembaliList();
            }
        },
    },
};
</script>
