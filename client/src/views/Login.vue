<template>
  <div class="login-container">
    <div class="login-box">
      <h2>Đăng nhập</h2>
      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="username">Tên đăng nhập</label>
          <input v-model="username" type="text" id="username" required placeholder="Nhập tên đăng nhập" />
        </div>
        <div class="form-group">
          <label for="password">Mật khẩu</label>
          <div class="password-input">
            <input v-model="password" :type="showPassword ? 'text' : 'password'" id="password" required
              placeholder="Nhập mật khẩu" />
            <i class="password-toggle" :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"
              @click="togglePassword"></i>
          </div>
        </div>
        <div v-if="error" class="error-message">{{ error }}</div>
        <button type="submit" class="login-button">Đăng nhập</button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      username: '',
      password: '',
      error: '',
      showPassword: false
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword
    },
    handleLogin() {
      // Thông tin đăng nhập cài đặt cứng
      const validUsername = 'admin.plt'
      const validPassword = '@123456'

      if (this.username === validUsername && this.password === validPassword) {
        // Sử dụng sessionStorage thay vì localStorage
        sessionStorage.setItem('isAuthenticated', 'true')
        // Chuyển hướng đến trang EmployeeList
        this.$router.push('/employees')
      } else {
        this.error = 'Tên đăng nhập hoặc mật khẩu không đúng'
      }
    }
  },
  created() {
    // Kiểm tra nếu đã đăng nhập thì chuyển hướng
    if (sessionStorage.getItem('isAuthenticated') === 'true') {
      this.$router.push('/employees')
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.login-box {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

h2 {
  text-align: center;
  color: #333;
  margin-bottom: 2rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.password-input {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input input {
  width: 100%;
  padding-right: 40px;
}

.password-toggle {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #666;
  font-size: 1.2rem;
}

.password-toggle:hover {
  color: #007bff;
}

label {
  color: #666;
  font-size: 0.9rem;
}

input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.login-button {
  background-color: #007bff;
  color: white;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.login-button:hover {
  background-color: #0056b3;
}

.error-message {
  color: #dc3545;
  font-size: 0.9rem;
  text-align: center;
}
</style>