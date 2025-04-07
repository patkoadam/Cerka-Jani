<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};


</script>

<template>
  <Head title="Log in" />

  <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
    {{ status }}
  </div>

  <form @submit.prevent="submit">
    <div class="login-wrapper">
      <div v-if="!isLoggedIn" class="login-container">
        <h2>Bejelentkezés</h2>

        <input v-model="form.email" type="email" placeholder="Email" required />
        <input v-model="form.password" type="password" placeholder="Jelszó" required />
        <button type="submit" class="btn-login">Bejelentkezés</button>

        <div class="register-link">
          <span>Nincs fiókod?</span>
          <Link :href="route('register')" class="btn-register" as="button">Regisztráció</Link>
        </div>
      </div>
      <div v-else class="welcome-container">
        <h2>Üdvözlünk, bejelentkezve!</h2>
        <button class="btn-logout" @click="logout">Kijelentkezés</button>
      </div>
    </div>
  </form>
</template>

<style scoped>
/* Átvett design elemek a korábbi stílus alapján */
:root {
  --primary-color: #1e3a8a;
  --secondary-color: #23395b;
  --background-color: #f0f4f8;
  --input-border: #ccc;
  --input-focus: #1e3a8a;
}

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
  color: #1e3a8a;
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
  color: #1e3a8a;
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

