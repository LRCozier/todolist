<template>
  <form
  @submit.prevent="handleSubmit"
  class="todo-form">
  <input
  type="text"
  v-model="title"
  placeholder="Title"
  required />



  </form>
</template>

<script lang="ts">

</script>

<style>

</style>