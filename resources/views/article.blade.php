<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl px-5 md:mt-14"
        aria-labelledby="hero-heading"
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
                >
                    <x-icons.right-arrow class="size-3 shrink-0 -scale-x-100" />
                    <div class="text-sm">Blog</div>
                </a>
            </div>

            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
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
                class="mt-12 text-3xl font-extrabold will-change-transform sm:text-4xl"
            >
                NativePHP for desktop v1 is finally here!
            </h1>

            {{-- Date --}}
            <div class="inline-flex items-center gap-1.5 pt-5 opacity-60">
                <x-icons.date class="size-5 shrink-0" />
                <div class="text-sm">April 9, 2025</div>
            </div>
        </header>

        {{-- Divider --}}
        <div class="my-5 h-px w-full rounded-full bg-current opacity-10"></div>

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
        >
            Lorem ipsum dolor sit amet consectetur adipisicing elit. At quam a
            animi rem et dolor quis culpa quo tempore fuga? Culpa amet eveniet
            fugit sit voluptatem, totam consequatur officia earum! Amet maxime
            explicabo, eum nulla eius sunt! Suscipit, iure? Ab, doloremque!
            Quibusdam cum iusto, aliquid tempora nulla cumque corrupti libero
            eveniet autem nihil doloremque vel omnis dolore exercitationem,
            facilis reprehenderit. Fuga fugiat deleniti, suscipit deserunt
            perspiciatis consequuntur tempora at cupiditate harum exercitationem
            inventore omnis debitis dolores dolorem aliquam molestias
            accusantium similique neque! Fugit voluptas temporibus maiores ad
            vel reiciendis perferendis. Nemo, maiores. Totam odio sint
            voluptates illum quis minus sapiente laborum quam voluptatem
            consectetur dolore dolor saepe tempore, nostrum aut ad inventore
            iusto blanditiis quia dicta, alias harum aspernatur ea? Dolores,
            sint voluptas fugit distinctio quidem facere quisquam nobis aliquam
            odio consequuntur nostrum laboriosam unde. Autem provident, nam
            voluptatibus distinctio ex possimus fuga earum labore, beatae qui
            adipisci itaque aliquam? Iste fugit dolorum expedita minus quis
            perspiciatis qui in odit quidem corrupti quod, tenetur facilis
            reprehenderit sunt ratione illo facere delectus illum quas,
            voluptatem tempore aspernatur excepturi. Ratione, ut nulla?
            Blanditiis fugit asperiores accusantium ducimus cupiditate quaerat
            odio ut consectetur assumenda illum doloremque corporis hic
            perferendis totam ea, delectus accusamus rem, soluta nulla
            necessitatibus natus qui? Sapiente repellendus unde minus! Provident
            ducimus nisi saepe consectetur eligendi minus nulla, et possimus
            quis, cumque autem, nam dicta deleniti enim. Sed adipisci ut
            molestias quibusdam iure ratione ipsam, veniam commodi, vitae
            consequatur quam? Obcaecati ut sequi vel nobis iste. Facere
            recusandae illo harum, repellat similique debitis minima ratione
            nihil minus voluptatem dolore veritatis quos iusto vel dignissimos
            architecto fugit optio nostrum praesentium suscipit. Quod unde amet
            blanditiis, a nam voluptatum tempora odit illo porro eius nulla
            ducimus minima labore non ipsam possimus delectus quo numquam.
            Nulla, itaque? Nostrum nam numquam quod eos eaque. Iste labore
            dolor, cumque voluptatem totam quia? Quod quisquam distinctio ab
            dolor mollitia. Maiores explicabo voluptatibus alias, incidunt
            soluta sed itaque, nihil sit beatae autem, voluptatum hic
            perspiciatis cumque non? Magnam error libero unde debitis dolorum
            minima doloremque aliquid nostrum repudiandae esse aliquam delectus
            asperiores, nulla accusamus cupiditate facilis temporibus veritatis
            illum sunt soluta ad ea porro. Optio, architecto illum. Ad aliquam
            sequi culpa optio, recusandae inventore, placeat fuga est quae ea
            deserunt laborum laudantium saepe veritatis harum repellat cumque
            architecto debitis consectetur ex quaerat. Excepturi maxime incidunt
            exercitationem eveniet? At, explicabo quam sint sunt est
            voluptatibus possimus officia modi delectus veritatis quisquam id?
            Distinctio enim tempora a asperiores, nulla autem sapiente sunt
            harum culpa earum minima! Soluta, in ullam? Est, veritatis iusto
            voluptates ipsum voluptatem ut deserunt, quo sint libero ducimus
            quisquam quam necessitatibus blanditiis magni debitis velit facilis
            fugit nesciunt error doloribus architecto, earum provident soluta
            officia. Saepe. Blanditiis aperiam voluptate incidunt, vitae autem
            eaque dolore quibusdam rem culpa ipsam. Esse quia beatae, itaque et
            expedita rem fugiat amet vitae. Repudiandae facere nihil beatae,
            nobis a quaerat odit. Excepturi laudantium doloremque reiciendis
            obcaecati, dolorum velit veniam quasi, eius quos quidem nobis fugit
            aspernatur ipsa aliquid dolorem necessitatibus a! Assumenda quasi
            sapiente velit deserunt, obcaecati quo incidunt quis ratione?
            Voluptatum molestias vel consequuntur atque necessitatibus quia
            debitis ea, a asperiores doloremque quidem? Cupiditate illo
            asperiores nulla maiores veniam. Quisquam tempore odio possimus
            magni obcaecati minus aspernatur corporis dicta deleniti. Ducimus
            eos dolore dolorem sit eligendi. Quidem deserunt illum eaque
            provident sed vero velit laborum aliquid animi ex nostrum, earum
            distinctio quas nisi tempora dolor quam consequatur explicabo rem
            quibusdam. Dolorem, error? Aperiam, earum, cumque dolore expedita
            reiciendis fugiat tenetur dolorum blanditiis debitis quae quo
            laborum rem quibusdam explicabo ipsam illum cum. Cupiditate aperiam,
            natus iste quibusdam aut nostrum veritatis?
        </article>
    </section>
</x-layout>
