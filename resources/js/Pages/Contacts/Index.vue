<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contacts</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <Link :href="route('contacts.create')" class="btn-blue mb-4">Add New Contact</Link>

                    <table class="min-w-full">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="contact in contacts" :key="contact.id">
                            <td>{{ contact.name }}</td>
                            <td>{{ contact.type }}</td>
                            <td>{{ contact.email }}</td>
                            <td>{{ contact.phone }}</td>
                            <td>
                                <Link :href="route('contacts.edit', contact.id)" class="btn-blue mr-2">Edit</Link>
                                <Link :href="route('contacts.show', contact.id)" class="btn-green mr-2">View</Link>
                                <button @click="deleteContact(contact.id)" class="btn-red">Delete</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        AuthenticatedLayout,
        Link
    },
    props: ['contacts'],
    methods: {
        deleteContact(id) {
            if (confirm('Are you sure you want to delete this contact?')) {
                this.$inertia.delete(route('contacts.destroy', id));
            }
        }
    }
}
</script>
