import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { LocalStorage, Loading, Notify } from 'quasar'

export const useUserStore = defineStore('user', {
  state: () => ({
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
      password: null,
      userType: null,
    },
  }),

  getters: {},

  actions: {
    async login() {
      try {
        Loading.show({ message: 'Loading...' })

        let response = await api.post('auth/login.php', this.formData)

        this.setUserData(response)
      } catch (error) {
        Notify.create({
          type: 'negative',
          position: 'top-right',
          message: 'Invalid Credentials',
        })
        console.log(error)
      }
    },

    async logout() {
      LocalStorage.clear()
      window.location.href = '/'
    },

    async register() {
      try {
        Loading.show({ message: 'Loading...' })

        let response = await api.post('register', this.formData)

        this.setUserData(response)
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

    async updateUser() {
      try {
        Loading.show({ message: 'Updating User...' })

        const response = await api.patch(`/user/${this.user.id}`, this.user)

        LocalStorage.set('user', response.data.user)

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
        password: user.password,
        userType: user.userType,
      }

      LocalStorage.set('user', this.user)
      LocalStorage.set('accessToken', userData.data.data.token)

      window.location.href = '/'
    },

    resetFormData() {
      Object.keys(this.formData).forEach((key) => {
        this.formData[key] = null
      })
    },
  },
})
