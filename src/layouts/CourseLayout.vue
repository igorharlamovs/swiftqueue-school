<template>
  <q-header elevated class="bg-grey-10" height-hint="56">
    <q-toolbar class="q-gutter-sm q-py-sm">
      <!-- Create Course Button -->
      <q-btn
        v-if="!userStore.isStudent"
        class="q-mr-sm"
        color="teal"
        @click="showCreateCourseDialog = true"
        text-color="white"
        unelevated
        icon="add"
        :label="$q.screen.gt.sm ? 'Create Course' : ''"
      >
        <q-tooltip v-if="!$q.screen.gt.sm" class="bg-teal">Create Course</q-tooltip>
      </q-btn>

      <q-space />

      <!-- Filter Course Status -->
      <q-select
        class="primary-shadow"
        label-color="orange"
        dark
        bg-color="dark"
        v-model="courseStore.filterCourseStatus"
        :options="initStore.courseStatusTypes"
        option-label="typeName"
        :emit-value="false"
        map-options
        label="Course Status"
        :behavior="$q.screen.lt.md ? 'dialog' : 'menu'"
        popup-content-class="teal-dropdown"
        filled
        :style="$q.screen.gt.sm ? 'width: 15%' : 'width: 40%'"
        clearable
      />

      <!-- Search Input -->
      <q-input
        v-model="courseStore.searchCourseName"
        dense
        standout
        dark
        placeholder="Search courses"
        :style="$q.screen.gt.sm ? 'width: 30%' : 'width: 80%'"
      >
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </q-toolbar>
  </q-header>

  <q-page class="row no-wrap" style="height: 100%">
    <!-- Course List -->
    <q-scroll-area class="q-mb-md" style="flex: 1 1 auto; min-height: 0" :bar-style="barStyle" :thumb-style="thumbStyle">
      <div class="row">
        <div class="col-xs-12" v-for="course in courseStore.searchedCourses" :key="course.id">
          <q-card class="my-card q-mx-xl q-my-md bg-dark text-white" flat bordered>
            <q-card-section class="dark50">
              <div class="text-h6">{{ course.name }}</div>
            </q-card-section>

            <q-list class="text-body1 q-my-sm" horizontal>
              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="primary" name="note" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Description</q-item-label>
                  <q-item-label class="text-orange" id="courseDesc">
                    {{ course.description }}
                  </q-item-label>
                </q-item-section>
              </q-item>

              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="green" name="timer" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Course Starting On</q-item-label>
                  <q-item-label class="text-orange" id="courseStart">
                    {{ course.startAt }}
                  </q-item-label>
                </q-item-section>
              </q-item>

              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="red" name="timer" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Course Ending On</q-item-label>
                  <q-item-label class="text-orange" id="courseEnd">
                    {{ course.endAt }}
                  </q-item-label>
                </q-item-section>
              </q-item>

              <q-item clickable>
                <q-item-section avatar>
                  <q-icon color="purple" name="menu" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Status</q-item-label>
                  <q-item-label class="text-orange" id="courseStatus">
                    {{ initStore.courseStatusTypeById(course.statusTypeId).typeName }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>

            <q-card-actions v-if="!userStore.isStudent && courseStore.belongsToTeacher(course.userId)" class="dark50" align="center">
              <q-btn square color="teal-8" size="12px" icon="edit" @click="handleEditCourse(course)">
                <q-tooltip class="bg-teal">Edit Course</q-tooltip>
              </q-btn>
              <q-btn square color="red-8" size="12px" icon="delete" @click="courseStore.deleteCourse(course.id)">
                <q-tooltip class="bg-red">Delete Course</q-tooltip>
              </q-btn>
            </q-card-actions>
          </q-card>
        </div>
      </div>
    </q-scroll-area>

    <!-- Create Course -->
    <q-dialog v-model="showCreateCourseDialog" :maximized="$q.screen.lt.md">
      <CourseForm v-model="courseStore.formData.course" :isEdit="false" @submit="createCourse" />
    </q-dialog>

    <!-- Edit Course -->
    <q-dialog v-model="showEditCourseDialog" :maximized="$q.screen.lt.md">
      <CourseForm v-model="editCourse" :isEdit="true" @submit="submitEditCourse" />
    </q-dialog>
  </q-page>
</template>

<script setup>
import { useQuasar } from 'quasar'
import { ref } from 'vue'
import { useCourseStore } from 'src/stores/courseStore.js'
import { useInitStore } from 'src/stores/initStore.js'
import { useUserStore } from 'src/stores/userStore.js'

// Components
import CourseForm from 'src/components/CourseForm.vue'

// Imports
const $q = useQuasar()
const courseStore = useCourseStore()
const initStore = useInitStore()
const userStore = useUserStore()

// State
const showCreateCourseDialog = ref(false)
const showEditCourseDialog = ref(false)
const editCourse = ref(null)

// Methods
const createCourse = async (course) => {
  await courseStore.createCourse(course)
  showCreateCourseDialog.value = false
}

const submitEditCourse = async (course) => {
  // Patch only the changed fields
  const response = await courseStore.editCourse(course)
  if (response) showEditCourseDialog.value = false
}

const handleEditCourse = (course) => {
  editCourse.value = { ...course }
  showEditCourseDialog.value = true
}

// Initial fetch
if (!courseStore.courses.length) {
  courseStore.getCourses()
}

// Scroll styles
const barStyle = {
  right: '2px',
  borderRadius: '9px',
  backgroundColor: 'teal',
  width: '9px',
  opacity: 0.1,
}

const thumbStyle = {
  right: '4px',
  borderRadius: '5px',
  backgroundColor: 'teal',
  width: '5px',
  opacity: 1,
}
</script>