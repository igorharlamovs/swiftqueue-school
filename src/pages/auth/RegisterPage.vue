<template>
  <q-page class="flex column items-center" :class="$q.screen.lt.md ? 'justify-start' : 'justify-center'">
    <q-card
      class="bg-dark"
      :class="$q.screen.lt.md ? 'q-pa-lg full-width full-height no-radius' : 'q-pa-xl'"
      :style="$q.screen.lt.md ? '' : 'width: 600px; box-shadow: 0 2px 12px rgba(0,0,0,0.8)'"
    >
      <!-- Header -->
      <div class="col row justify-center items-center" style="min-height: 100px">
        <div class="text-h4 text-teal"><q-icon name="person_add" /> Register Account</div>
      </div>

      <!-- Registration Form -->
      <q-form @submit="userStore.register()" @reset="userStore.resetFormData()" class="q-gutter-y-md">
        <q-input
          filled
          v-model="userStore.formData.name"
          label="Name *"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          :rules="[rules.required, rules.max255]"
        />

        <q-input
          filled
          v-model="userStore.formData.email"
          type="email"
          label="Email *"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          :rules="[rules.required, rules.max255]"
        />

        <q-input
          filled
          v-model="userStore.formData.password"
          type="password"
          label="Password *"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          :rules="[rules.required, rules.max255]"
        />

        <q-input
          filled
          v-model="userStore.formData.passwordConfirm"
          type="password"
          label="Confirm Password *"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          :rules="[rules.required, rules.max255, (val) => val === userStore.formData.password || 'Passwords must match']"
        />

        <!-- User Type (Admin, Student, Teacher) -->
        <q-select
          auto-width
          class="primary-shadow"
          label-color="orange"
          dark
          bg-color="dark"
          v-model="userStore.formData.userType"
          :options="initStore.userTypes"
          option-label="typeName"
          :emit-value="false"
          map-options
          label="User Type"
          :behavior="$q.screen.lt.md ? 'dialog' : 'menu'"
          popup-content-class="teal-dropdown"
          filled
          :rules="[rules.required]"
        />

        <!-- Actions -->
        <div class="row q-mt-lg justify-end">
          <q-btn label="Register" type="submit" color="teal" class="q-px-md" />
          <q-btn label="Reset" type="reset" color="orange" flat class="q-ml-sm q-px-md" />
        </div>
      </q-form>
    </q-card>
  </q-page>
</template>

<script setup>
import { validationRules as rules } from 'src/validation/genericRules.js'
import { useUserStore } from 'src/stores/userStore.js'
import { useInitStore } from 'src/stores/initStore.js'

const userStore = useUserStore()
const initStore = useInitStore()
</script>
