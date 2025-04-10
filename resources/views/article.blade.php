<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl px-5 md:mt-14"
        aria-labelledby="article-title"
    >
        <header class="grid place-items-center text-center">
            {{-- Back button --}}
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
            >
                <a
                    href="{{ route('blog') }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 will-change-transform hover:-translate-x-0.5 hover:opacity-100"
                    aria-label="Return to blog listing"
                >
                    <x-icons.right-arrow
                        class="size-3 shrink-0 -scale-x-100"
                        aria-hidden="true"
                    />
                    <div class="text-sm">Blog</div>
                </a>
            </div>

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
                NativePHP for desktop v1 is finally here!
            </h1>

            {{-- Date --}}
            <div
                class="inline-flex items-center gap-1.5 pt-4 opacity-60"
                aria-label="Publication date"
            >
                <x-icons.date
                    class="size-5 shrink-0"
                    aria-hidden="true"
                />
                <time
                    datetime="2025-04-09"
                    class="text-sm"
                >
                    April 9, 2025
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
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur
            facere dolore praesentium eius amet ex suscipit quam quibusdam
            rerum, ratione, veritatis quidem, repudiandae ipsum in. Dolore
            voluptatibus iusto saepe cum. Maiores tenetur nobis aliquid
            recusandae hic, illo, aliquam laudantium aspernatur iste commodi
            temporibus vero maxime, deserunt consequuntur fugiat animi dicta
            debitis alias quos amet facere repellendus? Nesciunt, fugiat? Vel,
            harum. Eos magnam, totam blanditiis nemo facilis culpa voluptate sed
            dolores delectus alias velit, deleniti ex id quasi. Maiores
            laboriosam repellendus vitae aliquam voluptas delectus deserunt
            provident. Saepe, ullam. Error, ipsam. Facere dolore ullam
            reprehenderit debitis et aperiam exercitationem numquam deserunt?
            Temporibus asperiores exercitationem commodi vel? Autem, optio?
            Tempora, dicta, pariatur dolores repudiandae corrupti beatae
            voluptate dignissimos omnis consectetur ratione rerum.
            Exercitationem eligendi, sint necessitatibus cumque voluptatum
            corrupti incidunt inventore natus cupiditate, obcaecati nisi unde
            nesciunt commodi! Eveniet itaque nihil ducimus repellendus et atque
            laborum quos? Nostrum, aperiam aut. Ratione, earum! Voluptate
            deleniti labore dolor quod nobis atque nam repellendus? Fugiat,
            aliquam voluptatum quam cum, veniam mollitia, autem consequatur
            officiis dolorem assumenda tempore. Distinctio officiis numquam
            omnis quos aperiam minima voluptatibus? Magnam laborum nesciunt eos
            qui sed repellendus tenetur harum, id, mollitia a provident
            accusantium sint architecto, laudantium sit dolore quia. Vel impedit
            quasi nam necessitatibus accusantium saepe praesentium laudantium
            ut. Sit dolor voluptas dignissimos doloremque qui atque dolorum.
            Aperiam eaque sapiente dicta nulla error laborum eius ex illum
            harum, dolor quae illo praesentium ad hic at dolorem iusto
            recusandae unde. Ipsa alias tenetur magni reprehenderit nam
            consequuntur pariatur consequatur quas aspernatur cumque harum ullam
            asperiores corporis distinctio consectetur dicta iusto, iure rem
            quos nobis laboriosam eos nulla accusamus et. Similique. Consectetur
            hic vel explicabo id assumenda, dolores quos neque asperiores ut,
            aperiam a blanditiis est, ullam officia cum error eligendi delectus!
            Cupiditate iusto est ad. Magni porro blanditiis quo delectus!
            Possimus quis repellat aliquam, quia repudiandae deserunt ipsum
            laudantium quaerat impedit veniam quibusdam rerum libero! Nam
            laboriosam qui blanditiis nihil soluta, magnam ut fuga voluptatem
            voluptatibus doloremque aut, aliquam velit? Soluta in esse dolorem
            harum excepturi incidunt qui omnis quidem perferendis, alias culpa
            ipsa quaerat delectus quam dolores nulla inventore dicta rerum enim
            obcaecati. Officia esse nostrum voluptate mollitia corporis. Numquam
            exercitationem fuga debitis soluta. Accusantium a voluptatum
            reprehenderit, perferendis dolorum sequi ab consequatur molestiae
            necessitatibus consequuntur asperiores expedita? Corporis vel fugiat
            distinctio sint magnam eveniet facilis. Corrupti, quisquam
            perferendis. Architecto recusandae dolores aspernatur eligendi
            laborum iure libero sit vitae optio error cupiditate illo magnam
            harum quam, porro debitis repellendus quis iusto nemo, atque nobis
            aliquam consequatur? Quo, saepe repudiandae. Rem nostrum quos illo
            eos cupiditate culpa eum dolorem debitis odit accusantium quibusdam
            eligendi ea quisquam, nam beatae, nihil vitae mollitia totam laborum
            necessitatibus veritatis porro error molestias. Exercitationem,
            soluta. Eaque suscipit amet impedit illum hic rerum nesciunt. Totam
            culpa, quia fuga at blanditiis dolorum rerum iusto ipsa quae
            distinctio a placeat dolorem omnis praesentium libero obcaecati
            molestiae porro aliquid. Accusantium itaque rerum nobis, quam, non
            numquam animi qui cupiditate repellendus repellat veritatis pariatur
            expedita debitis veniam beatae rem dicta vel vitae, eaque eos
            placeat. Consequatur, facilis commodi. Soluta, incidunt. Beatae
            nobis nesciunt quis reiciendis? Velit voluptatum et placeat
            accusantium illo suscipit id dolorum cupiditate rem fugiat! Libero
            esse, ad dolorum commodi officiis incidunt enim corrupti, fuga
            beatae aspernatur expedita? Earum rerum laborum dolore architecto?
            Culpa vitae at ipsum sapiente? Labore aliquid, dolor optio voluptas
            mollitia recusandae quas sequi tempora corporis. Ipsa voluptate
            fugiat omnis perferendis deserunt, itaque quos perspiciatis.
            Asperiores explicabo dolore, molestiae, consequatur sint soluta
            vitae quae iure reprehenderit hic officia aliquid omnis reiciendis
            voluptatibus tempora provident veniam in magni eum et exercitationem
            doloribus ullam. Neque, culpa temporibus.
        </article>
    </section>
</x-layout>
