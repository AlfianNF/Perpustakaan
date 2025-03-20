<template>
    <div class="flex">
      <Sidebar />
      <div class="flex-1 flex flex-col">
        <Navbar />
        <div class="flex-1 overflow-x-hidden overflow-y-auto p-6">
          <h1 class="text-xl font-semibold mb-4">Daftar Peminjaman</h1>
  
          <div class="mb-4 flex gap-4">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari buku atau user..."
              class="p-2 border rounded w-1/3"
              @keyup.enter="fetchPinjam"
            />
            <input
              v-model="filterDate"
              type="date"
              class="p-2 border rounded"
              @change="fetchPinjam"
            />
            <button
              @click="fetchPinjam"
              class="px-4 py-2 bg-blue-500 text-white rounded"
            >
              Cari
            </button>
            <button
              @click="fetchAddPinjam"
              class="px-4 py-2 bg-green-500 text-white rounded"
            >
              Tambah
            </button>
          </div>
  
          <div class="bg-white shadow rounded-md p-4">
            <table class="w-full border text-center">
              <thead>
                <tr class="bg-gray-100">
                  <th class="border px-4 py-2">No</th>
                  <th class="border px-4 py-2">User</th>
                  <th class="border px-4 py-2">Buku</th>
                  <th class="border px-4 py-2">Tanggal Pinjam</th>
                  <th class="border px-4 py-2">Tanggal Kembali</th>
                  <th class="border px-4 py-2">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(pinjam, index) in daftarPinjam" :key="pinjam.id">
                  <td class="border px-4 py-2">
                    {{ (currentPage - 1) * daftarPinjam.length + index + 1 }}
                  </td>
                  <td class="border px-4 py-2">{{ pinjam.user_pinjam.name }}</td>
                  <td class="border px-4 py-2">{{ pinjam.buku.title }}</td>
                  <td class="border px-4 py-2">
                    {{ formatTanggal(pinjam.tgl_pinjam) }}
                  </td>
                  <td class="border px-4 py-2">
                    {{ formatTanggal(pinjam.tgl_kembali) }}
                  </td>
                  <td class="border px-4 py-2">
                    <button
                      @click="editPinjam(pinjam.id)"
                      class="px-3 py-1 bg-blue-500 text-white rounded-md mr-2"
                    >
                      Edit
                    </button>
                    <button
                      @click="deletePinjam(pinjam.id)"
                      class="px-3 py-1 bg-red-500 text-white rounded-md"
                    >
                      Hapus
                    </button>
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
              <span>Halaman {{ currentPage }} dari {{ totalPages }}</span>
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
      </div>
    </div>
  
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
    >
      <div class="bg-white p-6 rounded-md w-1/2">
        <h2 class="text-2xl font-semibold mb-4">Tambah Peminjaman Baru</h2>
        <form @submit.prevent="submitPinjam">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">User ID</label>
            <input
              v-model="newPinjam.user_id"
              type="number"
              class="mt-1 p-2 border rounded-md w-full"
            />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Buku ID</label>
            <input
              v-model="newPinjam.buku_id"
              type="number"
              class="mt-1 p-2 border rounded-md w-full"
            />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">
              Tanggal Pinjam
            </label>
            <input
              v-model="newPinjam.tgl_pinjam"
              type="date"
              class="mt-1 p-2 border rounded-md w-full"
            />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">
              Tanggal Kembali
            </label>
            <input
              v-model="newPinjam.tgl_kembali"
              type="date"
              class="mt-1 p-2 border rounded-md w-full"
            />
          </div>
          <div class="flex justify-end gap-2">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 bg-gray-300 rounded-md"
            >
              Batal
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded-md"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import axios from "axios";

export default {
    name: "Pinjam",
    components: {
        Sidebar,
        Navbar,
    },
    data() {
        return {
            daftarPinjam: [],
            searchQuery: "",
            filterDate: "",
            currentPage: 1,
            totalPages: 1,
            baseUrl: "http://belajar.test/",
        };
    },
    mounted() {
        this.fetchPinjam();
    },
    methods: {
        async fetchPinjam() {
            try {
                const response = await axios.get(
                    `${this.baseUrl}api/pinjam/list`,
                    {
                        params: {
                            page: this.currentPage,
                            search: this.searchQuery || undefined,
                            tgl_pinjam: this.filterDate || undefined,
                        },
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                this.daftarPinjam = response.data.data.data; 
                this.totalPages = response.data.last_page || 1;

            } catch (error) {
                console.error("Gagal mengambil data pinjam:", error);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.fetchPinjam();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.fetchPinjam();
            }
        },
        async deletePinjam(id) {
            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                try {
                    await axios.delete(
                        `${this.baseUrl}api/pinjam/${id}/delete`,
                        {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem(
                                    "token"
                                )}`,
                            },
                        }
                    );
                    this.fetchPinjam();
                } catch (error) {
                    console.error("Gagal menghapus pinjam:", error);
                }
            }
        },
        formatTanggal(tanggal) {
            if (!tanggal) return "";
            const options = { day: "numeric", month: "long", year: "numeric" };
            return new Date(tanggal).toLocaleDateString("id-ID", options);
        },
    },
};
</script>
