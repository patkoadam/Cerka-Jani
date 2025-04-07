<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
  roles: {
    type: Array,
    default: () => [],
  },
});

// Űrlap adatainak kezelése
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: '',
});

// Opcionális visszajelzés (üzenet)
const message = '';
const success = true;

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="Register" />

  <div class="min-h-screen flex items-center justify-center bg-cornsilk">
    <form @submit.prevent="submit" class="w-full max-w-md bg-white p-8 rounded-lg shadow">
      <h2 class="text-2xl font-bold mb-6 text-blue-900 text-center">Regisztráció</h2>

      <div class="mb-4">
        <label for="name" class="block text-gray-700 mb-1">Név</label>
        <input
          v-model="form.name"
          type="text"
          id="name"
          required
          class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-600 bg-gray-50"
        />
      </div>

      <div class="mb-4">
        <label for="email" class="block text-gray-700 mb-1">Email cím</label>
        <input
          v-model="form.email"
          type="email"
          id="email"
          required
          class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-600 bg-gray-50"
        />
      </div>

      <div class="mb-4">
        <label for="role" class="block text-gray-700 mb-1">Szerepkör</label>
        <select
          v-model="form.role_id"
          id="role"
          required
          class="w-full p-2 border border-gray-300 rounded bg-gray-50 focus:outline-none focus:border-blue-600"
        >
          <option disabled value="">Válassz szerepkört</option>
          <option v-for="r in roles" :key="r.id" :value="r.id">
            {{ r.name }}
          </option>
        </select>
      </div>

      <div class="mb-4">
        <label for="password" class="block text-gray-700 mb-1">Jelszó</label>
        <input
          v-model="form.password"
          type="password"
          id="password"
          required
          class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-600 bg-gray-50"
        />
      </div>

      <div class="mb-4">
        <label for="password_confirmation" class="block text-gray-700 mb-1">Jelszó megerősítése</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          id="password_confirmation"
          required
          class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-600 bg-gray-50"
        />
      </div>

      <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
        Regisztráció
      </button>

      <div
        v-if="message"
        class="mt-4 p-2 rounded text-center"
        :class="{'bg-green-200 text-green-800': success, 'bg-red-200 text-red-800': !success}"
      >
        {{ message }}
      </div>
    </form>
  </div>
</template>


<style scoped>
.container {
  max-width: 500px;
}

:root {
  --primary-color: #1e3a8a;
  --secondary-color: #23395b;
  --background-color: #f0f4f8;
  --input-border: #ccc;
  --input-focus: #1e3a8a;
}

/* Ezek a stílusok adnak egyedi megjelenést a bejelentkező/regisztrációs oldalaknak */
.login-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background: var(--background-color);
  background-color: cornsilk;
}

.login-container,
.welcome-container {
  background: #fff;
  padding: 40px;
  border-radius: 10px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.login-container h2,
.welcome-container h2 {
  margin-bottom: 20px;
  color: var(--primary-color);
}

.login-container form {
  display: flex;
  flex-direction: column;
}

.login-container input {
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid var(--input-border);
  border-radius: 5px;
  font-size: 16px;
  background: #f9f9f9;
  color: #333;
}

.login-container input:focus {
  outline: none;
  border-color: var(--input-focus);
  box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
}

.btn-login,
.btn-logout,
.btn-register {
  background: var(--primary-color);
  color: #fff;
  border: none;
  padding: 12px;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.btn-login:hover,
.btn-logout:hover,
.btn-register:hover {
  background: var(--secondary-color);
}

.register-link {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.register-link span {
  margin-bottom: 10px;
  color: #555;
}
</style>
