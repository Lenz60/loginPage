<?= $this->extend('layout/login/template'); ?>
<?= $this->section('content'); ?>

<body>
    <?php $validation = \Config\Services::validation(); ?>
    <section class="bg-sky-600 ">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg shadow  md:mt-0 sm:max-w-md xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl ">
                        Create and account
                    </h1>
                    <form class="space-y-4 md:space-y-6" method="POST" action="/register" enctype="multipart/form-data">
                        <?= csrf_field(); ?>
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Your email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " placeholder="name@company.com" required="">
                            <small class="text-red-700"><?= $validation->getError('email'); ?></small>
                        </div>
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 ">Your name</label>
                            <input type="name" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " placeholder="John Doe" required="">
                            <small class="text-red-700"><?= $validation->getError('name'); ?></small>
                        </div>
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required="">
                            <small class="text-red-700"><?= $validation->getError('password'); ?></small>
                        </div>
                        <div>
                            <label for="confirm-password" class="block mb-2 text-sm font-medium text-gray-900 ">Confirm password</label>
                            <input type="password" name="confirm-password" id="confirm-password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " required="">
                            <small class="text-red-700"><?= $validation->getError('confirm-password'); ?></small>
                        </div>
                        <div>
                            <label for="avatar" class="block mb-2 text-sm font-medium text-gray-900 ">Avatar</label>
                            <input type="file" name="avatar" id="avatar" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50  focus:outline-none" aria-describedby="file_input_help">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="avatar_help">SVG, PNG, JPG (MAX. 800x400px).</p>
                            <small class="text-red-700"><?= $validation->getError('avatar'); ?></small>
                        </div>
                        <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Create an account</button>
                        <p class="text-sm font-light text-gray-500 ">
                            Already have an account? <a href="/login" class="font-medium text-primary-600 hover:underline ">Login here</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?= $this->endSection('content'); ?>