<x-layout title="Plugin Developer Terms and Conditions">
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
                Plugin Developer Terms and Conditions
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
                    datetime="2026-03-20"
                    class="text-sm"
                >
                    March 20, 2026
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
                These Plugin Developer Terms and Conditions ("Developer Terms")
                govern your participation as a plugin developer ("Developer",
                "you", "your") on the NativePHP Plugin Marketplace (the
                "Marketplace") operated by Bifrost Technology, LLC ("NativePHP",
                "we", "us", "our"). These Developer Terms are in addition to
                and supplement our general
                <a href="/terms-of-service">Terms of Service</a>.
            </p>

            <p>
                By submitting a plugin to the Marketplace, you acknowledge that
                you have read, understood, and agree to be bound by these
                Developer Terms. If you do not agree, you may not submit plugins
                for sale on the Marketplace.
            </p>

            <h2>1. Definitions</h2>

            <p>For the purposes of these Developer Terms:</p>

            <ul>
                <li>
                    <strong>"Plugin"</strong> means any software package,
                    extension, or add-on developed by you and submitted for
                    listing on the Marketplace.
                </li>
                <li>
                    <strong>"Customer"</strong> means any end user who purchases
                    or otherwise acquires a license to use a Plugin through the
                    Marketplace.
                </li>
                <li>
                    <strong>"Gross Sale Amount"</strong> means the total price
                    paid by a Customer for a Plugin, inclusive of any applicable
                    taxes but before the deduction of any fees.
                </li>
                <li>
                    <strong>"Platform Fee"</strong> means the commission retained
                    by NativePHP from the Gross Sale Amount of each Plugin sale.
                </li>
            </ul>

            <h2>2. Revenue Share and Platform Fee</h2>

            <p>
                NativePHP shall retain a Platform Fee of thirty percent (30%) of
                the Gross Sale Amount for each Plugin sale made through the
                Marketplace. The remaining seventy percent (70%) shall be paid
                to the Developer ("Developer Share"), subject to the payment
                terms set out in Section 3.
            </p>

            <p>
                The Platform Fee covers the costs of payment processing,
                hosting, distribution, platform maintenance, and related
                services provided by NativePHP.
            </p>

            <p>
                NativePHP reserves the right to modify the Platform Fee
                percentage upon thirty (30) days' written notice to the
                Developer. Continued participation in the Marketplace after such
                notice constitutes acceptance of the revised Platform Fee.
            </p>

            <h2>3. Payment Terms</h2>

            <p>
                Developer payouts shall be processed through Stripe Connect.
                As a condition of selling Plugins on the Marketplace, the
                Developer must complete the Stripe Connect onboarding process
                and maintain an active Stripe Connect account in good standing.
            </p>

            <p>
                Developer payouts are subject to a holding period of fifteen
                (15) days from the date of each sale. During this period, the
                sale proceeds are held by NativePHP to allow for the processing
                of any Customer refund requests. Following the holding period,
                the Developer Share will be transferred to the Developer's
                Stripe Connect account, subject to the Developer's account
                being in good standing.
            </p>

            <p>
                In the event that a refund is issued to a Customer during the
                holding period, the corresponding Developer Share for that sale
                will not be paid out. If a refund is issued after the Developer
                Share has been transferred, NativePHP reserves the right to
                deduct the refunded Developer Share amount from future payouts
                or to request repayment from the Developer.
            </p>

            <p>
                Payouts of the Developer Share are subject to the processing
                timelines and requirements of Stripe. NativePHP shall not be
                liable for any delays in payment caused by Stripe or the
                Developer's failure to maintain a valid payment account.
            </p>

            <p>
                The Developer is solely responsible for all tax obligations
                arising from their receipt of the Developer Share, including
                income taxes, value added taxes, goods and services taxes, or
                any other applicable levies in their jurisdiction.
            </p>

            <h2>4. Plugin Pricing and Discounts</h2>

            <p>
                NativePHP shall have the sole and absolute discretion to set,
                adjust, and determine the retail price of each Plugin listed on
                the Marketplace. The Developer acknowledges and agrees that
                NativePHP may:
            </p>

            <ul>
                <li>
                    Set the initial listing price of the Plugin;
                </li>
                <li>
                    Modify the retail price of the Plugin at any time without
                    prior notice to the Developer;
                </li>
                <li>
                    Offer discounts, promotional pricing, bundled pricing, or
                    other price reductions on the Plugin at NativePHP's sole
                    discretion;
                </li>
                <li>
                    Include the Plugin in platform-wide promotions, seasonal
                    sales, or other marketing campaigns.
                </li>
            </ul>

            <p>
                The Platform Fee and Developer Share shall be calculated based
                on the actual price paid by the Customer, which may differ from
                the standard listing price due to discounts or promotions
                applied by NativePHP.
            </p>

            <h2>5. Developer Responsibilities and Liability</h2>

            <p>
                The Developer is solely and entirely responsible for:
            </p>

            <ul>
                <li>
                    The development, quality, performance, maintenance, and
                    ongoing support of their Plugin;
                </li>
                <li>
                    Providing customer support to end users who purchase or use
                    the Plugin, including responding to bug reports, feature
                    requests, and technical issues;
                </li>
                <li>
                    Ensuring the Plugin does not infringe upon the intellectual
                    property rights of any third party;
                </li>
                <li>
                    Ensuring the Plugin complies with all applicable laws,
                    regulations, and industry standards;
                </li>
                <li>
                    Maintaining accurate and up-to-date documentation for
                    the Plugin;
                </li>
                <li>
                    Ensuring the Plugin does not contain malicious code,
                    vulnerabilities, or any functionality that could harm
                    Customers or their systems.
                </li>
            </ul>

            <p>
                NativePHP does not provide any support to Customers in relation
                to third-party Plugins. NativePHP shall have no obligation to
                assist Customers with installation, configuration, bug
                resolution, or any other matter relating to the Developer's
                Plugin.
            </p>

            <p>
                NativePHP accepts no liability whatsoever for the performance,
                reliability, security, compatibility, or fitness for purpose of
                any Plugin submitted by a Developer. The Developer shall
                indemnify and hold harmless NativePHP, its officers, directors,
                employees, and agents from and against any claims, damages,
                losses, liabilities, costs, or expenses (including reasonable
                legal fees) arising from or in connection with the Developer's
                Plugin or any breach of these Developer Terms.
            </p>

            <h2>6. Listing Criteria and Marketplace Standards</h2>

            <p>
                NativePHP reserves the right, in its sole and absolute
                discretion, to:
            </p>

            <ul>
                <li>
                    Establish, modify, and enforce criteria for the listing of
                    Plugins on the Marketplace, including but not limited to
                    technical standards, quality requirements, documentation
                    standards, and code review processes;
                </li>
                <li>
                    Change such listing criteria at any time, with or without
                    notice to the Developer;
                </li>
                <li>
                    Approve or reject any Plugin submitted for listing on the
                    Marketplace, for any reason or no reason;
                </li>
                <li>
                    Remove, suspend, or discontinue the listing of any Plugin
                    from the Marketplace at any time, for any reason or no
                    reason, including but not limited to quality concerns, policy
                    violations, inactivity, Customer complaints, or changes in
                    platform strategy;
                </li>
                <li>
                    Require the Developer to make modifications to their Plugin
                    as a condition of continued listing.
                </li>
            </ul>

            <p>
                NativePHP shall not be liable to the Developer for any losses,
                damages, or lost revenue resulting from the rejection, removal,
                suspension, or discontinuation of a Plugin listing.
            </p>

            <h2>7. Intellectual Property</h2>

            <p>
                The Developer retains all ownership rights in and to their
                Plugin, subject to the license granted herein. By submitting a
                Plugin to the Marketplace, the Developer grants NativePHP a
                non-exclusive, worldwide, royalty-free license to host,
                distribute, display, market, and promote the Plugin on the
                Marketplace and through NativePHP's marketing channels.
            </p>

            <p>
                The Developer represents and warrants that they have all
                necessary rights, licenses, and permissions to submit the Plugin
                to the Marketplace and to grant the license described above.
            </p>

            <h2>8. Data and Privacy</h2>

            <p>
                In the course of processing Plugin sales, certain Customer data
                (such as name, email address, and license information) may be
                shared with the Developer to facilitate the transaction and
                enable the Developer to provide support. The Developer agrees to
                handle all Customer data in compliance with applicable data
                protection laws, including but not limited to the General Data
                Protection Regulation (GDPR) and the California Consumer
                Privacy Act (CCPA).
            </p>

            <p>
                The Developer shall not use Customer data for any purpose other
                than providing support for and delivering updates to their
                Plugin, unless the Customer has provided separate, explicit
                consent.
            </p>

            <h2>9. Representations and Warranties</h2>

            <p>The Developer represents and warrants that:</p>

            <ul>
                <li>
                    They have the legal capacity and authority to enter into
                    these Developer Terms;
                </li>
                <li>
                    The Plugin is their original work or they have obtained all
                    necessary licenses and permissions;
                </li>
                <li>
                    The Plugin does not violate any applicable law or regulation;
                </li>
                <li>
                    The Plugin does not infringe upon any third party's
                    intellectual property rights;
                </li>
                <li>
                    All information provided to NativePHP in connection with
                    these Developer Terms is accurate and complete.
                </li>
            </ul>

            <h2>10. Termination</h2>

            <p>
                Either party may terminate these Developer Terms at any time
                by providing written notice to the other party. Upon
                termination:
            </p>

            <ul>
                <li>
                    The Developer's Plugins shall be removed from the
                    Marketplace within a reasonable time;
                </li>
                <li>
                    Existing Customer licenses for the Developer's Plugins shall
                    remain valid and enforceable;
                </li>
                <li>
                    NativePHP shall pay any outstanding Developer Share amounts
                    for sales completed prior to termination;
                </li>
                <li>
                    The Developer's obligations regarding indemnification,
                    intellectual property, and data protection shall survive
                    termination.
                </li>
            </ul>

            <p>
                NativePHP may terminate these Developer Terms immediately and
                without notice in the event of a material breach by the
                Developer.
            </p>

            <h2>11. Limitation of Liability</h2>

            <p>
                To the maximum extent permitted by law, NativePHP shall not be
                liable to the Developer for any indirect, incidental, special,
                consequential, or punitive damages, including but not limited to
                loss of profits, revenue, data, or business opportunities,
                arising from or related to these Developer Terms or the
                Developer's participation in the Marketplace.
            </p>

            <p>
                NativePHP's total aggregate liability under these Developer
                Terms shall not exceed the total Developer Share amounts paid to
                the Developer in the twelve (12) months preceding the event
                giving rise to the claim.
            </p>

            <h2>12. Amendments</h2>

            <p>
                NativePHP reserves the right to modify these Developer Terms at
                any time. We will provide notice of material changes by
                updating the "Last updated" date at the top of this page and,
                where practicable, by notifying the Developer via email. The
                Developer's continued participation in the Marketplace after
                such changes constitutes acceptance of the amended Developer
                Terms.
            </p>

            <h2>13. Waivers</h2>

            <p>
                No delay or failure to exercise any right or remedy provided for
                in these Developer Terms shall be deemed to be a waiver.
            </p>

            <h2>14. Severability</h2>

            <p>
                If any provision of these Developer Terms is held invalid or
                unenforceable, for any reason, by any arbitrator, court or
                governmental agency, department, body or tribunal, the remaining
                provisions shall remain in full force and effect.
            </p>

            <h2>15. Governing Law</h2>

            <p>
                These Developer Terms shall be governed by and construed in
                accordance with the laws of the State of Delaware, United States
                of America, without regard to its conflict of law provisions.
            </p>

            <h2>16. Entire Agreement</h2>

            <p>
                These Developer Terms, together with the
                <a href="/terms-of-service">Terms of Service</a> and
                <a href="/privacy-policy">Privacy Policy</a>, constitute the
                entire agreement between the Developer and NativePHP with
                respect to the Developer's participation in the Marketplace and
                supersede all prior or contemporaneous communications,
                agreements, and understandings, whether written or oral,
                relating to the subject matter herein.
            </p>

            <h2>Contact</h2>

            <p>
                If you have any questions regarding these Developer Terms,
                please contact us at
                <a href="mailto:support@nativephp.com">support@nativephp.com</a>.
            </p>
        </article>
    </section>
</x-layout>
