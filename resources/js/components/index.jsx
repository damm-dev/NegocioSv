import React from 'react';

const HeroSection = () => {
    return (
        <>
            {/* -------------------- 1. Barra de Navegación -------------------- */}
            <header className="navbar-custom">
                <div className="container-fluid d-flex justify-content-between align-items-center py-3 px-5">
                    
                    {/* Izquierda: Número de Contacto y Menú */}
                    <div className="d-flex align-items-center">
                        <button className="btn btn-outline-light me-3 border-0" type="button" aria-label="Toggle navigation">
                            <i className="bi bi-list fs-4"></i>
                        </button>
                        <span className="hotline-number">
                            <i className="bi bi-telephone-fill me-1"></i> +123 9888 0066
                        </span>
                    </div>

                    {/* Centro: Logo y Lema */}
                    <div className="text-center">
                        <h1 className="logo mb-0">mozzo</h1>
                        <p className="tagline text-uppercase m-0">Order. Food. Happy</p>
                    </div>

                    {/* Derecha: Carrito y Log In */}
                    <div className="d-flex align-items-center">
                        <a href="#" className="btn btn-outline-light rounded-circle me-3 cart-icon-btn position-relative">
                            <i className="bi bi-bag-fill fs-5"></i>
                            <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white text-dark border border-2 border-orange">
                                1/2
                            </span>
                        </a>
                        <a href="#" className="btn btn-outline-light rounded-pill border-2 px-3 fw-bold">
                            <i className="bi bi-person-fill me-1"></i> Log In
                        </a>
                    </div>
                </div>
            </header>

            {/* -------------------- 2. Sección Principal (Hero) -------------------- */}
            <section className="hero-section-custom text-white text-center pt-5 pb-5">
                <div className="container">
                    
                    {/* Texto Promocional */}
                    <p className="text-white-50 fs-6 fw-bold mb-2">Explore top-rated attractions, activities and more</p>
                    <h2 className="display-4 fw-bolder mb-5">Fast Delivery. Zero Hassle.</h2>

                    {/* Barra de Búsqueda */}
                    <div className="row justify-content-center mb-5">
                        <div className="col-12 col-lg-8">
                            <div className="input-group search-bar-custom shadow-lg rounded-pill p-1">
                                <span className="input-group-text bg-white border-0 ps-4">
                                    <i className="bi bi-geo-alt-fill text-muted"></i>
                                </span>
                                <input type="text" className="form-control border-0" placeholder="Enter delivery address" />
                                
                                {/* Botón Locate Me */}
                                <button className="btn btn-light rounded-pill mx-2 my-1 fw-bold locate-me-btn" type="button">Locate Me</button>
                                
                                {/* Botón Search */}
                                <button className="btn btn-primary rounded-pill search-btn-custom" type="button">
                                    <i className="bi bi-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Seis Categorías de Comida */}
                    <div className="row justify-content-center category-row-custom">
                        {/* Define las categorías en un array para evitar repetición si usas un map, 
                            pero para este ejemplo estático, las escribimos directamente. */}
                        
                        {/* Nota: En React, es mejor usar un array. Aquí están directas: */}
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🍔</div>
                            <p className="m-0 fw-bold">Burger</p>
                        </div>
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🍝</div>
                            <p className="m-0 fw-bold">Pasta</p>
                        </div>
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🥗</div>
                            <p className="m-0 fw-bold">Salad</p>
                        </div>
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🍕</div>
                            <p className="m-0 fw-bold">Pizza</p>
                        </div>
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🥪</div>
                            <p className="m-0 fw-bold">Sandwich</p>
                        </div>
                        <div className="col-4 col-sm-2 col-lg-2 mb-3 category-item">
                            <div className="icon-circle shadow-lg mx-auto mb-2 bg-white d-flex align-items-center justify-content-center fs-3">🍚</div>
                            <p className="m-0 fw-bold">Fried Rice</p>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
}

export default HeroSection;