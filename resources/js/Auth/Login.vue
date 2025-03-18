<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Login Perpustakaan</h2>

            <form @submit.prevent="handleLogin">
                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="no_induk">No Induk</label>
                    <input type="text" id="no_induk" v-model="no_induk"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.no_induk" class="text-red-500 text-sm mt-1">
                        {{ errors.no_induk[0] }}
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="password">Password</label>
                    <input type="password" id="password" v-model="password"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.password" class="text-red-500 text-sm mt-1">
                        {{ errors.password[0] }}
                    </p>
                </div>

                <p v-if="generalError" class="text-red-500 text-sm mb-3">{{ generalError }}</p>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600">
                    Login
                </button>
            </form>

            <p class="mt-4 text-gray-600 text-center">
                Belum punya akun? <a href="register" class="text-blue-500">Daftar</a>
            </p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Login',
    data() {
        return {
            no_induk: '',
            password: '',
            errors: null,
            generalError: ''
        };
    },
    methods: {
        async handleLogin() {
            try {
                const response = await axios.post('/api/login', {
                    no_induk: this.no_induk,
                    password: this.password,
                });

                console.log('Login berhasil:', response.data);
                localStorage.setItem('authToken', response.data.token);
                window.location.href = "/dashboard";

                this.errors = null;
                this.generalError = '';
            } catch (error) {
                console.error('Login gagal:', error.response ? error.response.data : error.message);

                if (error.response) {
                    if (error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else if (error.response.data.message) {
                        this.generalError = error.response.data.message;
                    } else {
                        this.generalError = 'Terjadi kesalahan saat login.';
                    }
                } else {
                    this.generalError = 'Tidak dapat terhubung ke server.';
                }
            }
        }
    }
};
</script>
