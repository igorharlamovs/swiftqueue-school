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
          Update Profile
        </div>
      </div>

      <!-- Profile Form -->
      <q-form @submit.prevent="submitForm" class="q-gutter-y-md">
        <q-input
          v-model="formData.name"
          label="User Name"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          filled
          :rules="[rules.required, rules.max255]"
        />

        <q-input
          v-model="formData.email"
          type="email"
          label="Email"
          input-style="color: white"
          label-color="orange"
          color="teal"
          class="primary-shadow"
          filled
          :rules="[rules.required, rules.max255]"
        />

        <q-select
          auto-width
          class="primary-shadow"
          label-color="orange"
          dark
          bg-color="dark"
          v-model="formData.userTypeId"
          :options="initStore.userTypes"
          option-label="typeName"
          option-value="id"
          emit-value
          map-options
          label="User Type"
          :behavior="$q.screen.lt.md ? 'dialog' : 'menu'"
          popup-content-class="teal-dropdown"
          filled
          :rules="[rules.required]"
        />

        <div class="row q-mt-lg justify-end">
          <q-btn label="Update" type="submit" color="teal" class="q-px-md" />
          <q-btn label="Logout" @click="userStore.logout" color="orange" flat class="q-ml-sm q-px-md" />
        </div>
      </q-form>

    </q-card>
  </q-page>
</template>

<script setup>
import { reactive } from 'vue'
import { useQuasar } from 'quasar'
import { useUserStore } from 'src/stores/userStore.js'
import { useInitStore } from 'src/stores/initStore.js'
import { validationRules as rules } from 'src/validation/genericRules.js'

const $q = useQuasar()
const userStore = useUserStore()
const initStore = useInitStore()

// Local reactive copy of the user for the form
const formData = reactive({
  id: userStore.user.id,
  name: userStore.user.name,
  email: userStore.user.email,
  userTypeId: userStore.user.userTypeId
})

// Submit handler
function submitForm() {
  // Push local form values to the store
  userStore.updateUser(formData)
}
</script>
