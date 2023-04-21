<?= $this->extend('layout/login/template'); ?>
<?= $this->section('content'); ?>

<body>
    <?php $validation = \Config\Services::validation(); ?>
    <section class="bg-sky-600 ">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg shadow  md:mt-0 sm:max-w-md xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl ">
                        Reset Password for <?= session()->get('email') ?>
                    </h1>
                    <?php
                    if (session()->getFlashdata('message-success')) {
                    ?>
                        <div class="bg-green-200 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?= session()->getFlashdata('message-success'); ?></span>
                        </div>
                    <?php
                    } elseif (session()->getFlashdata('message')) {
                    ?>
                        <div class="bg-red-200 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline"><?= session()->getFlashdata('message'); ?></span>
                        </div>
                    <?php
                    }
                    ?>
                    <form class="space-y-4 md:space-y-6" method="POST" action="/auth/reset" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div>
                            <label for="reset-password" class="block mb-2 text-sm font-medium text-gray-900 ">New password</label>
                            <input type="password" name="reset-password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required="">
                            <small class="text-red-700"><?= $validation->getError('reset-password'); ?></small>
                        </div>
                        <div>
                            <label for="reset-confirm-password" class="block mb-2 text-sm font-medium text-gray-900 ">Confirm password</label>
                            <input type="password" name="reset-confirm-password" id="confirm-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required="">
                            <small class="text-red-700"><?= $validation->getError('reset-confirm-password'); ?></small>
                        </div>
                        <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Reset password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?= $this->endSection('content'); ?>