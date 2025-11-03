import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Loading, Notify, Dialog } from 'quasar'
import { useUserStore } from './userStore'

export const useCourseStore = defineStore('course', {
  state: () => ({
    userStore: useUserStore(),

    formData: {
      course: {
        id: null,
        statusTypeId: null,
        name: null,
        description: null,
        startAt: null,
        endAt: null,
      },
    },

    courses: [],
    searchCourseName: null,
    filterCourseStatus: null,
  }),

  getters: {
    searchedCourses: (state) => {
      if (!state.searchCourseName && !state.filterCourseStatus) {
        return state.courses ?? []
      }

      return (state.courses ?? []).filter((course) => {
        const matchesName = state.searchCourseName
          ? course.name.toLowerCase().includes(state.searchCourseName.toLowerCase())
          : true

        const matchesStatus = state.filterCourseStatus
          ? course.statusTypeId === state.filterCourseStatus.id
          : true

        return matchesName && matchesStatus
      })
    },

    belongsToTeacher: (state) => (courseUserId) => {
      if(state.userStore.isAdmin) {
        return true
      }

      return courseUserId == state.userStore.user.id
    }
  },

  actions: {
    async getCourses() {
      try {
        Loading.show({ message: 'Loading Courses...' })

        const response = await api.get('/coursecrud')
        this.courses = response.data.courses

        Loading.hide()
      } catch (error) {
        Loading.hide()

        Dialog.create({
          title: 'Error',
          message: 'Failed to load courses. Please refresh.',
          ok: true,
          color: 'orange',
          dark: true,
        })

        console.log(error)
      }
    },

    async createCourse() {
      try {
        Loading.show({ message: 'Creating Course...' })

        const payload = {
          ...this.formData.course,
          userId: this.userStore.user.id
        }

        const response = await api.post('/coursecrud', payload)

        this.courses.push(response.data.course)

        Loading.hide()

        Notify.create({
          type: 'positive',
          message: 'Course created successfully',
          position: 'top',
        })

        this.resetFormData()

        return true
      } catch (error) {
        Loading.hide()

        Dialog.create({
          title: 'Error',
          message: 'Failed to create course. Please try again.',
          ok: true,
          color: 'orange',
          dark: true,
        })

        console.log(error)
        return false
      }
    },

    async editCourse(editCourse) {
      try {
        Loading.show({ message: 'Editing Course...' })

        //Double check that the editing course still exists
        const originalCourse = this.courses.find((w) => w.id === editCourse.id)
        if (!originalCourse) throw new Error('Course not found')

        const payload = {
          'editCourse': editCourse,
          'sessionUserId': this.userStore.user.id,
        }

        await api.patch(`/coursecrud/${editCourse.id}`, payload)
        await this.getCourses()

        Loading.hide()

        Notify.create({
          type: 'positive',
          message: 'Course updated successfully',
          position: 'top',
        })

        return true
      } catch (error) {
        Loading.hide()

        Dialog.create({
          title: 'Error',
          message: 'Failed to edit course. Please try again.',
          ok: true,
          color: 'orange',
          dark: true,
        })

        console.log(error)

        return false
      }
    },

    async deleteCourse(courseId) {
      Dialog.create({
        title: 'Delete course?',
        message: 'Are you sure you would like to delete the course?',
        cancel: true,
        persistent: false,
        dark: true,
      }).onOk(async () => {
        try {
          Loading.show({ message: 'Deleting Course...' })
          await api.delete(`/coursecrud/${courseId}`, { data: { userId: this.userStore.user.id } })
          this.courses = this.courses.filter((c) => c.id !== courseId)
          Loading.hide()

          Notify.create({
            type: 'positive',
            message: 'Course deleted successfully',
            position: 'top',
          })
        } catch (error) {
          Loading.hide()

          Dialog.create({
            title: 'Error',
            message: 'Failed to delete course. Please try again.',
            ok: true,
            color: 'negative',
          })

          console.log(error)
        }
      })
    },

    resetFormData() {
      Object.keys(this.formData.course).forEach((key) => {
        this.formData.course[key] = null
      })
    },
  },
})
