<template>
    <div>
        <v-row
            style="height: 91vh; background: url('/images/application/hero-small.jpg') no-repeat center center; background-size:cover !important"
            class="mt-n5 justify-center">
            <div class="text-center">
                <v-row class="text-center justify-center align-center mt-12">
                    <v-img max-width="250" src="/images/application/tootiko-Logo.png"></v-img>
                </v-row>
                <h1 class="display-1 text-center" style="line-height: 76px">
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


        <v-container>
            <v-row>
                <v-col cols="12" md="3" class="text-center">
                    <v-tooltip bottom color="purple">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-row justify="center">
                                    <v-img max-width="150" src="/images/application/step1.png"></v-img>
                                </v-row>
                                سفارش ترجمه ات رو ثبت کن
                            </div>
                        </template>
                        <span>روی دکمه ثبت سفارش کلیک کن و فایل یا لینک ترجمه ات رو همراه با اطلاعات سفارش به ما بده</span>
                    </v-tooltip>
                </v-col>
                <v-col cols="12" md="3" class="text-center">
                    <v-tooltip bottom color="purple">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-row justify="center">
                                    <v-img max-width="150" src="/images/application/step2.png"></v-img>
                                </v-row>
                                منتظر شمارش کلمات و مترجم باش
                            </div>
                        </template>
                        <span>چند دقیقه منتظر بمون تا کارشناسا کلمات رو شمارش کنن و همینطور یه مترجم مناسب با متن انتخاب کنن</span>
                    </v-tooltip>
                </v-col>
                <v-col cols="12" md="3" class="text-center">
                    <v-tooltip bottom color="purple">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-row justify="center">
                                    <v-img max-width="150" src="/images/application/step3.png"></v-img>
                                </v-row>
                                به راحتی پرداخت کن
                            </div>
                        </template>
                        <span>به راحتی با هر کارت عضو شتابی، هزینه مترجم رو پرداخت کن</span>
                    </v-tooltip>
                </v-col>
                <v-col cols="12" md="3" class="text-center">
                    <v-tooltip bottom color="success">
                        <template v-slot:activator="{ on }">
                            <div v-on="on">
                                <v-row justify="center">
                                    <v-img max-width="150" src="/images/application/step4.png"></v-img>
                                </v-row>
                                فایل ترجمه ات رو تحویل بگیر
                            </div>
                        </template>
                        <span>بعد از مهلتی که برای مترجم انتخاب کردی ، فایل ترجمه شده ات رو تحویل بگیر و طوطیکو رو به دوستات معرفی کن!</span>
                    </v-tooltip>
                </v-col>
            </v-row>
        </v-container>

        <v-container>
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
                            در صورتی که در زمینه ترجمه متخصص هستید، در آزمون ورودی طوطیکو شرکت کنید و سپس با ترجمه‌کردن کسب درآمد کنید.
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
                    <v-card shaped class="mx-auto">
                        <v-img src="/images/application/main-page-connection.svg"></v-img>
                        <v-card-title>
                            ترجمه مقالات خود را به حرفه‌ای‌ها بسپارید.
                        </v-card-title>
                        <v-card-subtitle>
                            مقالات و متون انگلیسی خود را به راحتی به مترجمین متخصص در حوزه مربوطه بسپارید.
                        </v-card-subtitle>
                        <v-card-actions>
                            <v-btn
                                color="purple" text
                            >
                                ثبت نام | ورود
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    export default {
        name: "HomePage"
    }
</script>

<style scoped>

</style>
