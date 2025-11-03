import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { LocalStorage, Loading, Notify } from 'quasar'
import { useInitStore } from './initStore'

export const useUserStore = defineStore('user', {
  state: () => ({
    initStore: useInitStore(),

    formData: {
      name: null,
      email: null,
      password: null,
      passwordConfirm: null,
      userType: null,
    },

    user: LocalStorage.getItem('user') || {
      id: null,
      name: null,
      email: null,
      userTypeId: null,
    },
  }),

  getters: {
    isStudent: (state) => state.initStore.userTypeById(state.user.userTypeId).typeVariable == 'student',
    isAdmin: (state) => state.initStore.userTypeById(state.user.userTypeId).typeVariable == 'admin'
  },

  actions: {
    async login() {
      try {
        Loading.show({ message: 'Loading...' })

        let response = await api.post('/login', this.formData)

        this.setUserData(response)
        
        window.location.href = '/'
      } catch (error) {
        Notify.create({
          type: 'negative',
          position: 'top-right',
          message: 'Invalid Credentials',
        })
        console.log(error)
        Loading.hide()
      }
    },

    async logout() {
      LocalStorage.clear()
      window.location.href = '/courses'
    },

    async register() {
      try {
        Loading.show({ message: 'Loading...' })

        let response = await api.post('/register', this.formData)

        this.setUserData(response)
        
        window.location.href = '/'
      } catch (error) {
        Notify.create({
          type: 'negative',
          position: 'top-right',
          message: 'Registration Failed',
        })
        console.log(error)
        Loading.hide()
      }
    },

    async updateUser(formData) {
      try {
        Loading.show({ message: 'Updating User...' })

        const response = await api.post('/updateprofile', formData)

        this.setUserData(response)

        Notify.create({
          type: 'positive',
          color: 'teal',
          position: 'top-right',
          message: 'User updated successfully!',
        })
      } catch (error) {
        Notify.create({
          type: 'negative',
          position: 'top-right',
          message: 'Failed to update user. Please try again.',
        })
        console.log(error)
      } finally {
        Loading.hide()
      }
    },

    setUserData(userData) {
      const user = userData.data.data.user

      if (!user) {
        console.warn('Invalid user data:', userData)
        return
      }

      this.user = {
        id: user.id,
        name: user.name,
        email: user.email,
        userTypeId: user.userTypeId,
      }

      LocalStorage.set('user', this.user)
      LocalStorage.set('accessToken', userData.data.data.token)
    },

    resetFormData() {
      Object.keys(this.formData).forEach((key) => {
        this.formData[key] = null
      })
    },
  },
})
