<template>
  <div>
    <!-- Navbar -->
    <header class="navbar">
      <h1 class="app-title">Task Management App</h1>
      <button @click="logout" class="logout-btn">Logout</button>
    </header>

    <!-- Main Content -->
    <div class="dashboard">
      <h1>Task Dashboard</h1>

      <!-- Form to Add Task -->
      <div v-if="showAddTaskForm" class="add-task-form">
        <input v-model="newTask.title" type="text" placeholder="Title" required />
        <textarea v-model="newTask.description" placeholder="Description" required></textarea>
        <select v-model="newTask.status">
          <option value="Belum Selesai">Belum Selesai</option>
          <option value="Selesai">Selesai</option>
        </select>
        <button @click="addTask">Add Task</button>
        <button @click="showAddTaskForm = false">Cancel</button>
      </div>

      <button @click="showAddTaskForm = true" v-if="!showAddTaskForm">Add Task</button>

      <!-- Filter Tasks -->
      <select v-model="statusFilter" @change="fetchTasks" class="filter">
        <option value="">All</option>
        <option value="Selesai">Selesai</option>
        <option value="Belum Selesai">Belum Selesai</option>
      </select>

      <!-- Task List -->
      <ul class="task-list">
        <li v-for="task in tasks" :key="task.id" class="task-item">
          <span>{{ task.title }} - {{ task.description }}</span>
          <select v-model="task.status" @change="updateTaskStatus(task.id, task.status)">
            <option value="Belum Selesai">Belum Selesai</option>
            <option value="Selesai">Selesai</option>
          </select>
          <button @click="deleteTask(task.id)" class="delete-btn">Delete</button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      tasks: [],
      statusFilter: "",
      newTask: {
        title: "",
        description: "",
        status: "Belum Selesai",
      },
      showAddTaskForm: false,
    };
  },
  methods: {
    fetchTasks() {
      const token = localStorage.getItem("token");
      const url = "http://localhost:8000/api/tasks";
      const params = this.statusFilter ? { status: this.statusFilter } : {};

      axios
        .get(url, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
          params,
        })
        .then((response) => {
          this.tasks = response.data;
        })
        .catch((error) => {
          console.error("Error fetching tasks:", error);
        });
    },
    updateTaskStatus(taskId, newStatus) {
      const token = localStorage.getItem("token");
      axios
        .put(
          `http://localhost:8000/api/tasks/${taskId}`,
          { status: newStatus },
          {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        )
        .then(() => {
          this.fetchTasks();
        })
        .catch((error) => {
          console.error("Error updating task status:", error);
        });
    },
    deleteTask(taskId) {
      const token = localStorage.getItem("token");
      axios
        .delete(`http://localhost:8000/api/tasks/${taskId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then(() => {
          this.fetchTasks();
        })
        .catch((error) => {
          console.error("Error deleting task:", error);
        });
    },
    addTask() {
      const newTask = {
        title: this.newTask.title, // Pastikan menggunakan this.newTask
        description: this.newTask.description,
        status: this.newTask.status, // Menggunakan status yang dipilih
      };

      const token = localStorage.getItem("token"); // Ambil token dari localStorage

      axios
        .post("http://localhost:8000/api/tasks", newTask, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          console.log("Task added:", response.data);
          this.tasks.push(response.data); // Menambahkan task ke daftar
          this.newTask.title = ""; // Reset form setelah task ditambahkan
          this.newTask.description = "";
          this.newTask.status = "Belum Selesai";
        })
        .catch((error) => {
          console.error("Error adding task:", error.response.data);
        });
    },
    logout() {
      localStorage.removeItem("token");
      this.$router.push("/");
    },
  },
  created() {
    this.fetchTasks();
  },
};
</script>

<style src="../assets/dashboard.css"></style>
