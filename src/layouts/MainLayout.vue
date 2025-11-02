<template>
  <q-layout view="lHh Lpr lFf" class="background">
    <q-footer bordered class="bg-grey-10 text-primary">
      <q-tabs no-caps active-color="primary" indicator-color="transparent" class="text-orange" v-model="tab">
        <q-route-tab v-if="userStore.user.id" name="courses" label="Courses" icon="book" to="/courses" exact />
        <q-route-tab v-if="userStore.user.id" name="profile" label="Profile" icon="account_circle" to="/profile" exact />
        <q-route-tab v-if="!userStore.user.id" name="login" label="Login" to="/login" exact />
        <q-route-tab v-if="!userStore.user.id" name="register" label="Register" to="/register" exact />
      </q-tabs>
    </q-footer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { onMounted } from 'vue'
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useUserStore } from 'src/stores/userStore'
import { useInitStore } from 'src/stores/initStore.js'
// import { LocalStorage } from 'quasar'

const userStore = useUserStore()
const route = useRoute()

const tab = ref(route.name)
const initStore = useInitStore()

onMounted(async () => {
  // Only run if userTypes is not already in LocalStorage
  // if (!LocalStorage.getItem('userTypes')) {
    await initStore.initialiseCommonLookups()
  // }
})

watch(
  () => route.name,
  (newName) => {
    tab.value = newName
  }
)
</script>
