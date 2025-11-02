<template>
  <q-page class="flex column items-center" :class="$q.screen.lt.md ? 'justify-start' : 'justify-center'">
    <q-card
      class="bg-dark"
      :class="$q.screen.lt.md ? 'q-pa-lg full-width full-height no-radius' : 'q-pa-xl'"
      :style="$q.screen.lt.md ? '' : 'width: 600px; box-shadow: 0 2px 12px rgba(0,0,0,0.8)'"
    >
      <!-- Header -->
      <div class="row justify-center items-center q-py-md q-mb-md">
        <div class="text-h5 text-teal text-center">
          <q-icon name="person" class="q-mr-sm" />
          Login
        </div>
      </div>

      <!-- Login Form -->
      <q-form @submit="userStore.login()" @reset="userStore.resetFormData()" class="q-gutter-y-md" autocomplete="off">
        <q-input
          v-model="userStore.formData.email"
          label="Email *"
          autocomplete="off"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          filled
        />

        <q-input
          v-model="userStore.formData.password"
          label="Password *"
          autocomplete="off"
          type="password"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          filled
        />

        <!-- Actions -->
        <div class="row q-mt-lg justify-end">
          <q-btn label="Login" type="submit" color="teal" class="q-px-md" />
          <q-btn label="Reset" type="reset" color="orange" flat class="q-ml-sm q-px-md" />
        </div>
      </q-form>
    </q-card>
  </q-page>
</template>

<script setup>
import { useQuasar } from 'quasar'
import { useUserStore } from 'src/stores/userStore.js'

const $q = useQuasar()
const userStore = useUserStore()
</script>
