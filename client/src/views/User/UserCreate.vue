<template>
    <div>
        <h1>Create New User</h1>
        <!-- User creation form goes here -->
    </div>
</template>

<script>
// import axios from '@/axios'; // Assuming you have a pre-configured axios instance

export default {
    name: 'UserCreate',
    data() {
        return {
            user: {
                username: '',
                email: '',
                password: ''
            },
            loading: false,
            error: null
        }
    },
    methods: {
        async createUser() {
            this.loading = true;
            this.error = null;
            try {
                // await axios.post('/users', this.user);
                alert('User created successfully!');
                this.$router.push('/users'); // Redirect to user list after creation
            } catch (error) {
                console.error('Error creating user:', error);
                if (error.response && error.response.data && error.response.data.message) {
                    this.error = error.response.data.message;
                } else {
                    this.error = 'Failed to create user.';
                }
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<style scoped>
/* Scoped styles for UserCreate */
</style>