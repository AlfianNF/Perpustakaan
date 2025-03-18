<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Registrasi Perpustakaan</h2>

            <form @submit.prevent="handleRegister">
                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="name">Nama Lengkap</label>
                    <input type="text" id="name" v-model="name"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="no_induk">No Induk</label>
                    <input type="text" id="no_induk" v-model="no_induk"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.no_induk" class="text-red-500 text-sm mt-1">{{ errors.no_induk[0] }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="email">Email</label>
                    <input type="email" id="email" v-model="email"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.email" class="text-red-500 text-sm mt-1">{{ errors.email[0] }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-2" for="password">Password</label>
                    <input type="password" id="password" v-model="password"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring focus:border-blue-400" required>
                    <p v-if="errors && errors.password" class="text-red-500 text-sm mt-1">{{ errors.password[0] }}</p>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600">Daftar</button>
            </form>

            <p class="mt-4 text-gray-600 text-center">
                Sudah punya akun? <a href="/" class="text-blue-500">Login</a>
            </p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Register',
    data() {
        return {
            name: '',
            no_induk: '',
            email: '',
            password: '',
            errors: null,
        };
    },
    methods: {
        async handleRegister() {
            try {
                const response = await axios.post('/api/register', {
                    name: this.name,
                    no_induk: this.no_induk,
                    email: this.email,
                    password: this.password,
                });
                console.log('Registrasi berhasil:', response.data);
                window.location.href = "/";
            } catch (error) {
                console.error('Registrasi gagal:', error.response ? error.response.data : error.message);
                if (error.response && error.response.data && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errors = null;
                }
            }
        },
    },
};
</script>