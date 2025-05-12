<template>
    <div>
        <h1>User List</h1>
        <!-- User list table and create button goes here -->
    </div>
</template>

<script>
// import axios from '@/axios'; // Assuming you have a pre-configured axios instance

export default {
    name: 'UserList',
    data() {
        return {
            users: [],
            loading: false,
            error: null
        }
    },
    methods: {
        async fetchUsers() {
            this.loading = true;
            this.error = null;
            try {
                // const response = await axios.get('/users');
                // this.users = response.data;
                // Placeholder data
                this.users = [
                    { id: 1, username: 'testuser1', email: 'test1@example.com' },
                    { id: 2, username: 'testuser2', email: 'test2@example.com' },
                ];
            } catch (error) {
                console.error('Error fetching users:', error);
                this.error = 'Failed to fetch users.';
            } finally {
                this.loading = false;
            }
        },
        editUser(userId) {
            this.$router.push({ name: 'UserEdit', params: { id: userId } });
        },
        async deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    // await axios.delete(`/users/${userId}`);
                    // Remove the user from the list
                    this.users = this.users.filter(user => user.id !== userId);
                } catch (error) {
                    console.error('Error deleting user:', error);
                    this.error = 'Failed to delete user.';
                }
            }
        }
    },
    created() {
        this.fetchUsers();
    }
}
</script>

<style scoped>
/* Scoped styles for UserList */
</style>