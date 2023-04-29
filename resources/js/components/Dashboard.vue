<template>
    <div>
        <router-view></router-view>
    </div>

</template>

<script>

export default {
    name: "Dashboard",
    mounted() {
        console.log('Mounted')
    }
}
</script>

