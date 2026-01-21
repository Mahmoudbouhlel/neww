<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import axios from "axios";
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const userType = ref("admin");
const step = ref(1);
const matricule = ref("");
const otp = ref("");
const loading = ref(false);
const error = ref("");
const success = ref("");
const canResend = ref(false);

const form = useForm({
  email: "",
  password: "",
  matricule: "",
  remember: false,
});

const submitMatricule = async () => {
  loading.value = true;
  error.value = "";
  try {
    await axios.post(route("user.otp.request"), { matricule: matricule.value });
    step.value = 2;
    success.value = "OTP sent to your phone.";
    setTimeout(() => (canResend.value = true), 10000);
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to send OTP";
  }
  loading.value = false;
};

const submitOtp = async () => {
  loading.value = true;
  error.value = "";
  try {
    const res = await axios.post(route("user.otp.verify"), { otp: otp.value });
    if (res.data.success) {
      window.location.href = res.data.redirect || route("user.dashboard");
    }
  } catch (err) {
    error.value = err.response?.data?.message || "OTP verification failed";
  }
  loading.value = false;
};

const resendOtp = async () => {
  canResend.value = false;
  await submitMatricule();
};

const submit = () => {
  if (userType.value === "user") {
    if (step.value === 1) {
      submitMatricule();
    } else {
      submitOtp();
    }
  } else {
    form.post(route("login"), {
      onSuccess: () => {
        axios.get(route("auth.user")).then((response) => {
          const userRole = response.data.role;
          if (userRole === "super_admin") {
            window.location.href = route("admin.dashboard");
          } else if (userRole === "agent") {
            window.location.href = route("agent.dashboard");
          } else {
            window.location.href = route("dashboard");
          }
        });
      },
      onFinish: () => form.reset("password"),
    });
  }
};
</script>

<template>
  <GuestLayout>
    <Head title="Login" />

    <!-- ======================= -->
    <!-- Leoni Logo Watermark BG -->
    <!-- ======================= -->
    <div class="absolute inset-0 -z-30 flex items-center justify-center opacity-10">
      <img
        src="\public\images\logo.png"
        alt="Leoni Logo"
        class="w-[60%] max-w-[520px] select-none pointer-events-none"
      />
    </div>

    <div class="w-full max-w-md mx-auto text-white animate-fade-in-up relative">

      <!-- Animated gradient background blobs -->
      <div class="absolute inset-0 overflow-hidden opacity-10 -z-10">
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-0 right-20 w-96 h-96 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
      </div>

      <!-- Header -->
      <div class="text-center mb-8">
        <h2 class="text-4xl font-bold bg-gradient-to-r from-indigo-300 to-purple-400 bg-clip-text text-transparent mb-2">
          WELCOME TO LEONI SEVICE MANAGEMENT
        </h2>

      </div>

      <!-- Status -->
      <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-300 text-sm text-center backdrop-blur-sm">
        {{ status }}
      </div>

      <!-- OTP Success/Error -->
      <div v-if="success" class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/50 text-green-300 text-sm text-center backdrop-blur-sm">
        {{ success }}
      </div>
      <div v-if="error" class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/50 text-red-300 text-sm text-center backdrop-blur-sm">
        {{ error }}
      </div>

      <!-- Toggle -->
      <div class="flex items-center bg-gray-800/50 rounded-full p-1 mb-8">
        <button
          @click="userType = 'admin'; step = 1"
          :class="userType === 'admin' ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg' : 'text-white/70 hover:text-white'"
          class="flex-1 py-2 px-6 rounded-full text-sm font-medium transition-all duration-300"
        >
          Admin/Agent
        </button>

        <button
          @click="userType = 'user'; step = 1"
          :class="userType === 'user' ? 'bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg' : 'text-white/70 hover:text-white'"
          class="flex-1 py-2 px-6 rounded-full text-sm font-medium transition-all duration-300"
        >
          User
        </button>
      </div>

      <!-- FORM -->
      <form @submit.prevent="submit" class="space-y-6 bg-gray-900/50 p-8 rounded-2xl border border-gray-700/50 shadow-2xl backdrop-blur-lg">

        <!-- Email (Admin) -->
        <div v-if="userType !== 'user'" class="space-y-2">
          <InputLabel for="email" value="Email" class="text-white/80 font-medium" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full bg-gray-800/50 border-gray-700/50"
            v-model="form.email"
            required
            autocomplete="username"
            placeholder="your@email.com"
          />
          <InputError class="mt-1 text-red-400/90 text-sm" :message="form.errors.email" />
        </div>

        <!-- Matricule (User Step 1) -->
        <div v-if="userType === 'user' && step === 1" class="space-y-2">
          <InputLabel for="matricule" value="Matricule" class="text-white/80 font-medium" />
          <TextInput
            id="matricule"
            type="text"
            class="mt-1 block w-full bg-gray-800/50 border-gray-700/50"
            v-model="matricule"
            required
            placeholder="Enter your matricule"
          />
          <InputError class="mt-1 text-red-400/90 text-sm" :message="form.errors.matricule" />
        </div>

        <!-- OTP (User Step 2) -->
        <div v-if="userType === 'user' && step === 2" class="space-y-2">
          <InputLabel for="otp" value="OTP" class="text-white/80 font-medium" />
          <TextInput
            id="otp"
            maxlength="6"
            class="mt-1 block w-full bg-gray-800/50 border-gray-700/50"
            v-model="otp"
            required
            placeholder="Enter OTP"
          />
          <InputError class="mt-1 text-red-400/90 text-sm" :message="form.errors.otp" />
        </div>

        <!-- Password (Admin) -->
        <div v-if="userType !== 'user'" class="space-y-2">
          <div class="flex items-center justify-between">
            <InputLabel for="password" value="Password" class="text-white/80 font-medium" />
            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="text-xs text-indigo-400 hover:text-indigo-300 hover:underline"
            >
              Forgot password?
            </Link>
          </div>
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full bg-gray-800/50 border-gray-700/50"
            v-model="form.password"
            required
            placeholder="••••••••"
          />
          <InputError class="mt-1 text-red-400/90 text-sm" :message="form.errors.password" />
        </div>

        <!-- Remember -->
        <div v-if="userType !== 'user'" class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" class="h-4 w-4 rounded border-gray-600 bg-gray-700/50" />
          <span class="ml-2 text-sm text-white/80">Remember me</span>
        </div>

        <!-- Submit -->
        <PrimaryButton
          class="w-full flex justify-center py-3 px-4 rounded-full shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 transition-all duration-300"
          :class="{ 'opacity-70': form.processing || loading }"
          :disabled="form.processing || loading"
        >
          <span v-if="!(form.processing || loading)">
            {{ userType === 'user' && step === 1 ? 'Send OTP' : userType === 'user' && step === 2 ? 'Verify OTP' : 'Sign in' }}
          </span>
          <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </PrimaryButton>

        <!-- Resend OTP -->
        <div v-if="userType === 'user' && step === 2 && canResend" class="mt-4">
          <button
            @click.prevent="resendOtp"
            class="w-full py-3 px-4 border border-indigo-500/50 rounded-full text-sm text-indigo-300 hover:bg-indigo-500/20 transition-all duration-300"
            :disabled="loading"
          >
            Resend OTP
          </button>
        </div>
      </form>

      <p class="mt-6 text-center text-xs text-white/50">
        By continuing, you agree to our Terms of Service and Privacy Policy.
      </p>

    </div>
  </GuestLayout>
</template>

<style scoped>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
.animate-blob { animation: blob 7s infinite ease-in-out; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }

input:focus {
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
  transition: all 0.2s ease;
}
</style>
