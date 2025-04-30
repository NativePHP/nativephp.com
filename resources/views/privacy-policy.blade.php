<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl px-5 md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute right-1/2 top-0 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>

            {{-- Primary Heading --}}
            <h1
                id="article-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-8 text-3xl font-extrabold will-change-transform sm:text-4xl"
            >
                Privacy Policy
            </h1>

            {{-- Date --}}
            <div
                class="inline-flex items-center gap-1.5 pt-4 opacity-60"
                aria-label="Publication date"
            >
                Last updated
                <x-icons.date
                    class="size-5 shrink-0"
                    aria-hidden="true"
                />
                <time
                    datetime="2025-04-30"
                    class="text-sm"
                >
                    April 30, 2025
                </time>
            </div>
        </header>

        {{-- Divider --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="flex items-center pb-3 pt-3.5 will-change-transform"
            aria-hidden="true"
        >
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
            <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
        </div>

        {{-- Content --}}
        <article
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="prose mt-2 max-w-none text-gray-600 will-change-transform dark:text-gray-400"
            aria-labelledby="article-title"
        >
            <p>
                Bifrost Technology, LLC operates the nativephp.com website, which provides the Service.
            </p>

            <p>
                This page is used to inform website visitors regarding our policies with the collection, use, and
                disclosure of Personal Information if anyone decided to use our Service, the NativePHP website.
            </p>

            <p>
                If you choose to use our Service, then you agree to the collection and use of information in relation with
                this policy. The Personal Information that we collect are used for providing and improving the Service.
                We will not use or share your information with anyone except as described in this Privacy Policy.
            </p>

            <p>
                The terms used in this Privacy Policy have the same meanings as in our
                <a href="/terms-of-service">Terms of Service</a>,
                unless otherwise defined in this Privacy Policy.
            </p>

            <h2>Owner Of The Website</h2>

            <p>
                Bifrost Technology, LLC<br>
                1111B S Governors Ave STE 2838<br>
                Dover<br>
                Delaware<br>
                19904
            </p>

            <p>
                support@nativephp.com
            </p>

            <h2>Information Collection and Use</h2>

            <p>
                For a better experience while using our Service, we may require you to provide us with certain
                personally identifiable information, including but not limited to your name and email. We only ask for
                the essential information we need to operate.
            </p>

            <h2>Log Data</h2>
            <p>
                We want to inform you that whenever you visit our Service, we collect information that your browser
                sends to us that is called Log Data. This Log Data may include information such as your computer’s
                Internet Protocol ("IP") address, browser version, pages of our Service that you visit, the time and
                date of your visit, the time spent on those pages, and other statistics.
            </p>

            <h2>Cookies</h2>
            <p>
                Cookies are files with small amount of data that is commonly used an anonymous unique identifier.
                These are sent to your browser from the website that you visit and are stored on your computer’s
                hard drive.
            </p>

            <p>
                Our website uses these "cookies" to collection information and to improve our Service. You have the
                option to either accept or refuse these cookies, and know when a cookie is being sent to your computer.
                If you choose to refuse our cookies, you may not be able to use some portions of our Service.
            </p>

            <p>
                For more general information on cookies, please read
                <a href="https://www.cookiesandyou.com">https://www.cookiesandyou.com</a>.
            </p>

            <h2>Service Providers</h2>
            <p>
                We may employ third-party companies and individuals due to the following reasons:
            </p>

            <ul>
                <li>To facilitate our Service;</li>
                <li>To provide the Service on our behalf;</li>
                <li>To perform Service-related services;</li>
                <li>or To assist us in analyzing how our Service is used.</li>
            </ul>

            <p>
                We want to inform our Service users that these third parties have access to your Personal Information.
                The reason is to perform the tasks assigned to them on our behalf. However, they are obligated not to
                disclose or use the information for any other purpose.
            </p>

            <p>
                For further information, you should consult the privacy policies of these third-parties directly.
            </p>

            <h2>Security</h2>
            <p>
                We value your trust in providing us your Personal Information, thus we are striving to use commercially
                acceptable means of protecting it. But remember that no method of transmission over the internet, or
                method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.
            </p>

            <h2>Links to Other Sites</h2>
            <p>
                Our Service may contain links to other sites. If you click on a third-party link, you will be directed
                to that site. Note that these external sites are not operated by us. Therefore, we strongly advise you
                to review the Privacy Policy of these websites. We have no control over, and assume no responsibility
                for the content, privacy policies, or practices of any third-party sites or services.
            </p>

            <h2>Children's Privacy</h2>
            <p>
                Our Services do not address anyone under the age of 13. We do not knowingly collect personal identifiable
                information from children under 13. In the case we discover that a child under 13 has provided us with
                personal information, we immediately delete this from our servers. If you are a parent or guardian and you
                are aware that your child has provided us with personal information, please contact us so that we will be
                able to take the necessary actions.
            </p>

            <h2>Changes to This Privacy Policy</h2>
            <p>
                We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically
                for any changes. We will notify you of any changes by posting the new Privacy Policy on this page.
                These changes are effective immediately, after they are posted on this page.
            </p>

            <h1>Cookie Policy</h1>
            <p>
                Bifrost Technology, LLC ("us", "we", or "our") uses cookies on nativephp.com (the "Service").
                By using the Service, you consent to the use of cookies.
            </p>

            <p>
                Our Cookie Policy explains what cookies are, how we use cookies, how third-parties we may partner with
                may use cookies on the Service, your choices regarding cookies and further information about cookies.
            </p>

            <h2>What Are Cookies</h2>
            <p>
                Cookies are small pieces of text sent to your web browser by a website you visit. A cookie file is
                stored in your web browser and allows the Service or a third-party to recognize your browser and make
                your next visit easier and the Service more useful to you.
            </p>

            <p>
                Cookies can be "persistent" - they are persisted between browsing sessions - or "session" cookies,
                which are deleted by your browser when you end your session or after a certain time limit.
            </p>

            <p>
                Cookies are associated with the browser, not the person, so they do not usually store sensitive
                information about you such as credit cards or bank details, photographs or personal information etc.
                The data they keep are of a technical nature, statistics, personal preferences, personalization of
                contents etc.
            </p>

            <h2>How We Use Cookies</h2>
            <p>
                When you use and access the Service, we may place a number of cookie files in your web browser.
            </p>

            <p>
                We use cookies for the following purposes:
            </p>
            <ul>
                <li>to enable certain functions of the Service,</li>
                <li>to provide analytics,</li>
                <li>to store your preferences,</li>
                <li>to enable advertisements delivery.</li>
            </ul>

            <p>
                We use both session and persistent cookies on the Service and we use different types of cookies to run the Service:
            </p>

            <ul>
                <li>
                    <strong>Essential/Technical Cookies</strong>
                    <p>
                        These allow the proper functioning of the web features. Allow the user to navigate through a web page,
                        platform or application and the use of different options or services that exist in it, such as controlling
                        traffic and data communication, identifying the session, access restricted access parts, remember the
                        elements that make up an order, perform the purchase process of an order, make the request for registration
                        or participation in an event, use security elements during navigation, store contents for dissemination of
                        videos or sound or share content through social networks.
                    </p>
                </li>
                <li>
                    <strong>Analysis Cookies</strong>
                    <p>
                        Those that are well treated by us or by third parties, allow us to quantify the number of users
                        and thus perform the measurement and statistical analysis of the use made by users of the
                        service offered. For this, your browsing on our website is analyzed in order to improve the
                        offer of products or services we offer.
                    </p>
                </li>
                <li>
                    <strong>Third-party Cookies</strong>
                    <p>
                        The Site may use third-party services that, on behalf of Google, will collect information for
                        statistical purposes, the use of the site by the user and for the provision of other services
                        related to the website activity and other services from Internet.
                    </p>
                </li>
            </ul>

            <h2>Manage And Reject</h2>
            <p>
                At any time, you can adapt the browser settings to manage, disregard the use of Cookies and be notified
                before they are downloaded.
            </p>

            <p>
                If you'd like to delete cookies or instruct your web browser to delete or refuse cookies, please visit
                the help pages of your web browser.
            </p>

            <p>
                Please note, however, that if you delete cookies or refuse to accept them, you might not be able to use
                all of the features we offer, you may not be able to store your preferences, and some of our pages
                might not display properly.
            </p>
        </article>
    </section>
</x-layout>
