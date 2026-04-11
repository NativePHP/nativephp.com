<x-layout>
    <div class="flex items-center justify-center pt-12 pb-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-4 relative z-0 overflow-hidden bg-gray-200/40 dark:bg-mirage/50 p-6 rounded-2xl transition duration-300 group hover:bg-violet-100 dark:hover:bg-violet-500/10">
            <!-- Blur decoration -->
            <div class="absolute -left-10 -top-10 -z-50 h-3/4 w-40 rounded-full bg-violet-50 opacity-0 blur-3xl transition duration-300 group-hover:opacity-100 dark:bg-white/15"></div>
            <div>
                <h2 class="text-center text-3xl font-semibold leading-relaxed text-gray-900 dark:text-white">
                    Sign in to your account
                </h2>
                <p class="mt-0.5 text-center text-sm text-gray-600 dark:text-gray-300">
                    Or
                    <a href="#" class="font-medium text-violet-600 hover:text-violet-500 dark:text-violet-400 dark:hover:text-violet-300 transition duration-300">
                        create a new account
                    </a>
                </p>
            </div>
            <form class="mt-4 space-y-3" action="{{ route('login.process') }}" method="POST">
                @csrf

                @error('email')
                <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-300">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p>{{ $message }}</p>
                    </div>
                </div>
                @enderror
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="appearance-none rounded-none relative block w-full px-4 py-2.5 border border-gray-300
                            dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900
                            dark:text-white bg-white/80 dark:bg-gray-800/50 rounded-t-md focus:outline-none focus:ring-violet-500
                            dark:focus:ring-violet-400 focus:border-violet-500 dark:focus:border-violet-400 focus:z-10 sm:text-sm transition duration-200"
                            placeholder="Email address" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none rounded-none relative block w-full px-4 py-2.5 border border-gray-300
                            dark:border-gray-700 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900
                            dark:text-white bg-white/80 dark:bg-gray-800/50 rounded-b-md focus:outline-none focus:ring-violet-500
                            dark:focus:ring-violet-400 focus:border-violet-500 dark:focus:border-violet-400 focus:z-10 sm:text-sm transition duration-200"
                            placeholder="Password">
                    </div>
                </div>

                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 text-violet-600 dark:text-violet-500 focus:ring-violet-500 dark:focus:ring-violet-400
                            border-gray-300 dark:border-gray-700 rounded dark:bg-gray-800/50 transition duration-200">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-200">
                            Remember me
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-violet-600 hover:text-violet-500 dark:text-violet-400 dark:hover:text-violet-300 transition duration-300">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium
                        rounded-xl text-white bg-violet-600 hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-800
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 dark:focus:ring-violet-400
                        dark:focus:ring-offset-gray-800 transition duration-300">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-violet-500 group-hover:text-violet-400 dark:text-violet-300 dark:group-hover:text-violet-200" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
