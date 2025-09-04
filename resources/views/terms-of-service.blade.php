<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
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
                Terms of Service
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
        <x-divider />

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
            class="prose mt-2 max-w-none text-gray-600 will-change-transform dark:text-gray-400 dark:prose-headings:text-white"
            aria-labelledby="article-title"
        >
            <p>
                Please read these Terms of Service ("Terms", "Terms and
                Conditions", "Terms of Use") carefully before using the
                nativephp.com website (the "Service", "Platform") operated by
                Bifrost Technology, LLC ("us", "we", or "our").
            </p>

            <p>
                Your access to and use of the Service is conditional, based on
                your acceptance of and compliance with these Terms. These Terms
                apply to all visitors, users and others who access or use the
                Service.
            </p>

            <p>
                By accessing or using the Service you agree to be bound by these
                Terms. If you disagree with any part of the terms, you may not
                access the Service. You should not create an account and you
                should leave this website.
            </p>

            <h2>Code Of Conduct</h2>
            <ul>
                <li>No illegal activities</li>
                <li>No fraud</li>
                <li>No spam and data mining</li>
                <li>No advertising</li>
                <li>No exploitation</li>
                <li>No impersonation</li>
                <li>No activities related to bots</li>
                <li>No use other than the intended</li>
            </ul>

            <p>
                Any violation to this basic and common sense rules means the
                deletion of your account and associated data.
            </p>

            <p>
                Whether conduct violates our Code of Conduct will be determined
                in our sole discretion.
            </p>

            <h2>Age Restriction</h2>
            <p>
                The platform is available to persons 18 years old or older. If
                you are between 13 and 18 years old, you may still use the
                Platform, but you must have a parent's or guardian's permission.
            </p>

            <p>
                By using the Platform, you confirm that you are least 18 years
                old, or 13 years old with the permission of your parents or
                guardians and that you can provide proof of this permission on
                request.
            </p>

            <p>
                If you are under 13 years old, you may not use our Platform in
                any manner.
            </p>

            <h2>Intellectual Property</h2>
            <p>
                The content on the Platform, including all information,
                software, technology, data, logos, marks, designs, text,
                graphics, pictures, audio and video files, other data or
                copyrightable materials or content, and their selection and
                arrangement, is referred to herein as "NativePHP Content", and
                is and remains the sole property of Bifrost Technology, LLC.
                NativePHP Content, including our trademarks, may not be modified
                by you in any way.
            </p>

            <h2>Account Ownership</h2>

            <p>
                We have the right to request additional information from You to
                determine account ownership.
            </p>

            <p>
                The information that We may request to assist in resolving
                ownership disputes includes, but is not limited to, the
                following:
            </p>
            <ul>
                <li>A copy of Your photo ID</li>
                <li>Your billing information and details</li>
                <li>Certified copies of your tax forms</li>
            </ul>

            <p>
                We reserve the right to determine the account ownership in its
                sole judgment, and the ability to transfer the account to the
                rightful owner, unless otherwise prohibited by law.
            </p>

            <h2>Refund Policy</h2>
            <p>
                Due to the nature of the Service, no returns are accepted.
                Refunds are offered on a case-by-case basis at our sole
                discretion.
            </p>

            <h2>Publicity</h2>
            <p>
                You grant Us the right to include Your company's name and/or
                logo as a customer on our website and other advertising and
                promotional materials. You may retract this right by giving
                written notice to
                <a href="mailto:support@nativephp.com">support@nativephp.com</a>
                .
            </p>

            <p>
                Within thirty business days after such notice, We will remove
                Your company's name from nativephp.com and will no longer
                include the name/logo in any of Our advertising or promotional
                materials.
            </p>

            <h2>Cancellation And Deletion</h2>

            <p>
                If You cancel a paid plan, the cancellation will become
                effective at the end of the then-current billing cycle. When You
                cancel a paid plan, Your account will revert to a free account
                and We may disable access to features available only to paid
                plan users.
            </p>

            <p>You may delete Your account at any time.</p>

            <p>
                Accounts on paid plans will be considered active accounts unless
                You explicitly ask us to delete Your account.
            </p>

            <p>
                If Your account is deleted, Your Content may no longer be
                available and all licenses granted will terminate.
            </p>

            <p>
                We are not responsible for the loss of such content upon
                deletion.
            </p>

            <p>
                We shall not be liable to any party in any way for the inability
                to access Content arising from any cancellation or deletion,
                including any claims of interference with business or
                contractual relations.
            </p>

            <h2>Warranties</h2>
            <p>
                nativephp.com is provided as-is. We cannot guarantee that
                unexpected errors will not prevent normal use of the Service and
                that the software wil be accessible 100% of the time, although
                every effort is made to reduce the likelihood of such issues.
            </p>

            <p>
                We reserve the right to amend the Platform, and any service or
                material we provide on the Platform, in our sole discretion
                without notice. We will not be liable if for any reason all or
                any part of the Platform is unavailable at any time or for any
                period.
            </p>

            <h2>Changes</h2>
            <p>
                We reserve the right, at our sole discretion, to modify or
                replace these Terms at any time. If a revision is made we will
                try to provide at least 30 days notice prior to any new terms
                taking effect. What constitutes a material change will be
                determined at our sole discretion.
            </p>

            <h2>Waivers</h2>
            <p>
                No delay or failure to exercise any right or remedy provided for
                in this Agreement will be deemed to be a waiver.
            </p>

            <h2>Severability</h2>
            <p>
                If any provision of this Agreement is held invalid or
                unenforceable, for any reason, by any arbitrator, court or
                governmental agency, department, body or tribunal, the remaining
                provisions will remain in effect.
            </p>

            <h2>Governing Law</h2>
            <p>
                This Agreement will be governed by and construed in accordance
                with the laws of the United States of America.
            </p>

            <h2>Contact</h2>
            <p>
                If you have any questions regarding these or the practices of
                this Site, please contact us at
                <a href="mailto:support@nativephp.com">support@nativephp.com</a>
                .
            </p>
        </article>
    </section>
</x-layout>
