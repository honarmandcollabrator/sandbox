<template>
    <div>
        <v-row style="height: 85vh" class="text-center justify-center align-center">
            <div>
                <v-row class="text-center justify-center align-center">
                <v-img class="text-center" max-width="250" src="/images/application/tootiko-Logo.png"></v-img>
                </v-row>
                <h1 class="display-2 text-center" style="line-height: 76px">
                    به سامانه سفارش آنلاین ترجمه
                    <span class="font-weight-bold" style="color: forestgreen">طوطیکو</span>
                    خوش آمدید
                    <br>
                </h1>
                <v-btn color="green darken-3 text-center" dark large class="mt-3">
                    ثبت سفارش ترجمه
                </v-btn>
            </div>
        </v-row>


        <v-row align="center">
            <v-col cols="12" md="6">
                <v-card
                    shaped
                    class="mx-auto"
                >
                    <v-img
                        src="/images/application/main-page-find-jobs.svg"
                    ></v-img>
                    <v-card-title>
                        کسب درآمد از ترجمه
                    </v-card-title>
                    <v-card-subtitle>
                        در آزمون طوطیکو شرکت کنید و سپس با ترجمه کردن کسب درآمد کنید
                    </v-card-subtitle>
                    <v-card-actions>

                        <v-btn
                            color="purple"
                            text
                        >
                            ترجمه
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card
                    shaped
                    class="mx-auto"
                >
                    <v-img
                        src="/images/application/main-page-connection.svg"
                    ></v-img>
                    <v-card-title>
                        به راحتی مقالات خود را ترجمه کنید
                    </v-card-title>
                    <v-card-subtitle>
                        مقالات و متون انگلیسی خود را به راحتی ترجمه کنید.
                    </v-card-subtitle>
                    <v-card-actions>
                        <v-btn
                            color="purple"
                            text
                        >
                            ثبت نام | ورود
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    export default {
        name: "HomePage"
    }
</script>

<style scoped>

</style>
