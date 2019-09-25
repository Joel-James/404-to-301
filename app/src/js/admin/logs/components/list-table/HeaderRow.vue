<template>
    <tr>
        <td id="cb" class="manage-column column-cb check-column">
            <input type="checkbox" value="">
        </td>
        <th scope="col" class="manage-column column-primary">Title</th>
        <th scope="col" class="manage-column">Author</th>
        <th scope="col" class="manage-column sortable">Host</th>
        <th scope="col" class="manage-column sortable">Date</th>
    </tr>
</template>

<script>
export default {

    name: 'HeaderRow',

    data () {
        return {

        };
    }
};
</script>