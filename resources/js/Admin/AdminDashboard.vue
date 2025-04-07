<template>
  <div class="flex">
      <Sidebar />
      <div class="flex-1 flex flex-col">
          <Navbar />
          <div class="flex flex-row flex-wrap justify-between px-4">
              <div class="w-full sm:w-1/2 lg:w-1/3">
                  <div class="bg-white rounded-lg p-4 mx-1 shadow-lg">
                      <DashboardChart
                          :chart-data="highlightedUserChartData"
                          :chart-options="defaultChartOptions('Data User', 'user')"
                          v-if="userChartData.datasets[0].data.length > 0"
                      />
                  </div>
              </div>

              <div class="w-full sm:w-1/2 lg:w-1/3">
                  <div class="bg-white rounded-lg shadow-lg p-4 mx-1">
                      <DashboardChart
                          :chart-data="highlightedPinjamChartData"
                          :chart-options="defaultChartOptions('Data Peminjaman', 'pinjam')"
                          v-if="pinjamChartData.datasets[0].data.length > 0"
                      />
                  </div>
              </div>

              <div class="w-full sm:w-1/2 lg:w-1/3">
                  <div class="bg-white rounded-lg shadow-lg p-4 mx-1">
                      <DashboardChart
                          :chart-data="highlightedKembaliChartData"
                          :chart-options="defaultChartOptions('Data Pengembalian', 'kembali')"
                          v-if="kembaliChartData.datasets[0].data.length > 0"
                      />
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";
import DashboardChart from "./DashboardContent.vue";
import axios from "axios";

export default {
  name: "AdminDashboard",
  components: {
      Sidebar,
      Navbar,
      DashboardChart,
  },
  data() {
      return {
          clickedIndex: {
              user: null,
              pinjam: null,
              kembali: null,
          },
          userChartData: {
              labels: ["Admin", "User"],
              datasets: [
                  {
                      backgroundColor: ["#FF6384", "#36A2EB"],
                      data: [],
                  },
              ],
          },
          pinjamChartData: {
              labels: ["Dipinjam", "Dikembalikan"],
              datasets: [
                  {
                      backgroundColor: ["#FFCE56", "#4BC0C0"],
                      data: [],
                  },
              ],
          },
          kembaliChartData: {
              labels: ["Ada Denda", "Tidak Ada Denda"],
              datasets: [
                  {
                      backgroundColor: ["#9966FF", "#FF9F40"],
                      data: [],
                  },
              ],
          },
      };
  },
  computed: {
      highlightedUserChartData() {
          return this.getHighlightedChartData(this.userChartData, this.clickedIndex.user);
      },
      highlightedPinjamChartData() {
          return this.getHighlightedChartData(this.pinjamChartData, this.clickedIndex.pinjam);
      },
      highlightedKembaliChartData() {
          return this.getHighlightedChartData(this.kembaliChartData, this.clickedIndex.kembali);
      },
  },
  mounted() {
      this.fetchDashboardData();
  },
  methods: {
      getHighlightedChartData(baseChartData, activeIndex) {
          const originalColors = baseChartData.datasets[0].backgroundColor;
          return {
              ...baseChartData,
              datasets: [
                  {
                      ...baseChartData.datasets[0],
                      backgroundColor: originalColors.map((color, i) =>
                          activeIndex === null || activeIndex === i
                              ? color
                              : color + "80"
                      ),
                  },
              ],
          };
      },
      defaultChartOptions(title, key) {
          return {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  title: {
                      display: true,
                      text: title,
                      font: { size: 16 },
                  },
                  legend: {
                      position: "bottom",
                  },
              },
              onClick: (e, elements) => {
                  if (elements.length > 0) {
                      const index = elements[0].index;
                      this.clickedIndex[key] = index;
                  } else {
                      this.clickedIndex[key] = null;
                  }
              },
          };
      },
      async fetchDashboardData() {
          try {
              const token = localStorage.getItem("token");
              const headers = { Authorization: `Bearer ${token}` };

              const [usersRes, pinjamRes, kembaliRes] = await Promise.all([
                  axios.get("http://belajar.test/api/user/list", {
                      headers,
                  }),
                  axios.get("http://belajar.test/api/pinjam/list", {
                      headers,
                  }),
                  axios.get("http://belajar.test/api/kembali/list", {
                      headers,
                  }),
              ]);

              const users = usersRes.data.data?.data ?? [];
              const pinjams = pinjamRes.data.data?.data ?? [];
              const kembalis = kembaliRes.data.data?.data ?? [];

              const adminCount = users.filter(user => user.is_admin === true).length;
              const userCount = users.filter(user => user.is_admin === false).length;
              this.userChartData.datasets[0].data = [adminCount, userCount];

              const dipinjam = pinjams.filter(p => p.status === "dipinjam").length;
              const dikembalikan = pinjams.filter(p => p.status === "dikembalikan").length;
              this.pinjamChartData.datasets[0].data = [dipinjam, dikembalikan];

              const adaDenda = kembalis.filter(k => k.denda && k.denda > 0).length;
              const tanpaDenda = kembalis.filter(k => !k.denda || k.denda == 0).length;
              this.kembaliChartData.datasets[0].data = [adaDenda, tanpaDenda];
          } catch (error) {
              console.error("Gagal mengambil data:", error);
              if (error.response?.status === 401) {
                  this.$router.push("/login");
              }
          }
      },
  },
};
</script>

<style scoped>
.p-4 {
  padding: 1rem;
}
</style>
