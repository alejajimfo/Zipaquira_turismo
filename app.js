// ============================================
// app.js - Aplicación Principal Completa
// Plataforma Turística Zipaquirá
// ============================================

// Configuración de la API
const API_URL = window.location.origin + '/zipaquira-turismo/api';

console.log('🔧 API_URL configurada:', API_URL);
console.log('🌐 window.location.origin:', window.location.origin);
console.log('🔗 URL completa login:', `${API_URL}/usuarios/login.php`);

// Componente principal de la aplicación
class ZipaquiraTuristicaApp extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            currentUser: null,
            currentView: 'home',
            showAuth: false,
            mobileMenuOpen: false,
            selectedRole: '',
            isRegistering: false,
            authData: { 
                email: '', 
                password: '', 
                confirmPassword: '',
                nombre_completo: '',
                telefono: '',
                direccion: '',
                ciudad: 'Zipaquirá',
                pais: 'Colombia',
                aceptacion_terminos: false,
                aceptacion_habeas_data: false
            },
            reviews: [],
            newReview: { rating: 5, comment: '' },
            loading: false,
            error: null,
            servicios: [],
            misServicios: [],
            misReservas: [],
            reservasRecibidas: [],
            programas: [],
            showReservaModal: false,
            servicioAReservar: null,
            reservaFormData: {
                fecha_reserva: '',
                hora_reserva: '',
                numero_personas: 1,
                notas_turista: ''
            },
            showServiceForm: false,
            serviceFormData: {
                nombre_servicio: '',
                rnt: '',
                descripcion: '',
                direccion: '',
                telefono: '',
                email: '',
                horario_apertura: '',
                horario_cierre: '',
                precio_desde: '',
                precio_hasta: ''
            },
            showEditModal: false,
            servicioAEditar: null,
            fotosServicio: [],
            nuevaFoto: { url_foto: '', descripcion: '', es_principal: 0 },
            editModalTab: 'info'
        };

        this.userRoles = [
            { id: 'tourist', name: 'Turista o Viajero', icon: 'fa-users', dbValue: 'turista' },
            { id: 'agency', name: 'Agencia de Turismo', icon: 'fa-building', dbValue: 'agencia' },
            { id: 'operator', name: 'Operador Turístico', icon: 'fa-map-marked-alt', dbValue: 'operador' },
            { id: 'restaurant', name: 'Restaurante', icon: 'fa-utensils', dbValue: 'restaurante' },
            { id: 'hotel', name: 'Hospedaje', icon: 'fa-hotel', dbValue: 'hotel' },
            { id: 'government', name: 'Institución Gubernamental', icon: 'fa-landmark', dbValue: 'gobierno' }
        ];
    }

    componentDidMount() {
        const savedUser = localStorage.getItem('currentUser');
        if (savedUser) {
            const user = JSON.parse(savedUser);
            this.setState({ currentUser: user }, () => {
                if(['agency', 'operator', 'restaurant', 'hotel'].includes(user.role)) {
                    this.loadMisServicios();
                    this.loadReservasRecibidas();
                }
                if(user.tipo_usuario === 'turista') {
                    this.loadMisReservas();
                }
            });
        }
        
        this.loadReviews();
        this.loadServicios();
        this.loadProgramas();
    }

    loadServicios = async () => {
        try {
            const response = await fetch(`${API_URL}/servicios/listar.php`);
            const data = await response.json();
            console.log('📦 Servicios cargados:', data);
            if (data.success) {
                this.setState({ servicios: data.data });
            }
        } catch (error) {
            console.error('Error cargando servicios:', error);
        }
    }

    loadMisServicios = async () => {
        const { currentUser } = this.state;
        
        console.log('Cargando mis servicios para usuario:', currentUser);
        
        if (!currentUser || !currentUser.id) {
            console.warn('No hay usuario o no tiene ID');
            return;
        }

        try {
            const url = `${API_URL}/servicios/mis_servicios.php?usuario_id=${currentUser.id}`;
            console.log('URL de mis servicios:', url);
            
            const response = await fetch(url);
            const data = await response.json();
            
            console.log('Respuesta mis servicios:', data);
            
            if (data.success) {
                console.log(`${data.total} servicios cargados`);
                this.setState({ misServicios: data.data });
            } else {
                console.warn('Error en respuesta:', data.message);
            }
        } catch (error) {
            console.error('Error cargando mis servicios:', error);
        }
    }

    loadProgramas = async () => {
        try {
            const response = await fetch(`${API_URL}/programas/listar.php`);
            const data = await response.json();
            if (data.success) {
                this.setState({ programas: data.data });
            }
        } catch (error) {
            console.error('Error cargando programas:', error);
        }
    }

    loadMisReservas = async () => {
        const { currentUser } = this.state;
        if (!currentUser || !currentUser.id) return;

        try {
            const response = await fetch(`${API_URL}/reservas/mis_reservas.php?turista_id=${currentUser.id}`);
            const data = await response.json();
            if (data.success) {
                this.setState({ misReservas: data.data });
            }
        } catch (error) {
            console.error('Error cargando reservas:', error);
        }
    }

    loadReservasRecibidas = async () => {
        const { currentUser } = this.state;
        
        console.log('=== CARGAR RESERVAS RECIBIDAS ===');
        console.log('Usuario actual:', currentUser);
        
        if (!currentUser || !currentUser.id) {
            console.warn('No hay usuario o no tiene ID');
            return;
        }

        try {
            const url = `${API_URL}/reservas/reservas_proveedor.php?usuario_id=${currentUser.id}`;
            console.log('URL reservas recibidas:', url);
            
            const response = await fetch(url);
            const data = await response.json();
            
            console.log('Respuesta reservas recibidas:', data);
            
            if (data.success) {
                console.log(`${data.data.length} reservas recibidas cargadas`);
                this.setState({ reservasRecibidas: data.data });
            } else {
                console.warn('Error en respuesta:', data.message);
            }
        } catch (error) {
            console.error('Error cargando reservas recibidas:', error);
        }
    }

    loadReviews = async () => {
        try {
            const response = await fetch(`${API_URL}/resenas/listar.php`);
            const data = await response.json();
            if (data.success) {
                this.setState({ reviews: data.data });
            }
        } catch (error) {
            console.error('Error cargando reseñas:', error);
        }
    }

    handleRegister = async () => {
        const { authData, selectedRole } = this.state;
        
        if (!authData.nombre_completo || !authData.email || !authData.password) {
            alert('Por favor completa los campos obligatorios');
            return;
        }

        if (authData.password !== authData.confirmPassword) {
            alert('Las contraseñas no coinciden');
            return;
        }

        if (!authData.aceptacion_terminos || !authData.aceptacion_habeas_data) {
            alert('Debes aceptar los términos y la política de Habeas Data');
            return;
        }

        try {
            this.setState({ loading: true });
            const roleInfo = this.userRoles.find(r => r.id === selectedRole);

            const payload = {
                tipo_usuario: roleInfo?.dbValue,
                email: authData.email,
                password: authData.password,
                nombre_completo: authData.nombre_completo,
                telefono: authData.telefono || null,
                direccion: authData.direccion || null,
                ciudad: authData.ciudad,
                pais: authData.pais,
                aceptacion_terminos: authData.aceptacion_terminos,
                aceptacion_habeas_data: authData.aceptacion_habeas_data
            };

            const response = await fetch(`${API_URL}/usuarios/registrar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                alert('¡Usuario registrado exitosamente!');
                this.setState({ isRegistering: false });
            } else {
                alert(data.message || 'Error al registrar usuario');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleLogin = async () => {
        const { authData } = this.state;
        
        if (!authData.email || !authData.password) {
            alert('Por favor ingresa tu email y contraseña');
            return;
        }

        try {
            this.setState({ loading: true });
            
            const payload = {
                email: authData.email,
                password: authData.password
            };

            const response = await fetch(`${API_URL}/usuarios/login.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                const tipoToRole = {
                    'turista': 'tourist',
                    'agencia': 'agency',
                    'operador': 'operator',
                    'restaurante': 'restaurant',
                    'hotel': 'hotel',
                    'gobierno': 'government'
                };
                
                const user = {
                    id: data.usuario.id,
                    role: tipoToRole[data.usuario.tipo_usuario] || 'tourist',
                    name: data.usuario.nombre_completo,
                    email: data.usuario.email,
                    tipo_usuario: data.usuario.tipo_usuario
                };

                this.setState({
                    currentUser: user,
                    showAuth: false,
                    currentView: 'dashboard'
                });

                localStorage.setItem('currentUser', JSON.stringify(user));
                
                if(['agency', 'operator', 'restaurant', 'hotel'].includes(user.role)) {
                    this.loadMisServicios();
                    this.loadReservasRecibidas();
                }
                if(user.tipo_usuario === 'turista') {
                    this.loadMisReservas();
                }
            } else {
                alert(data.message || 'Error al iniciar sesión');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleLogout = () => {
        this.setState({
            currentUser: null,
            currentView: 'home'
        });
        localStorage.removeItem('currentUser');
    }

    setView = (view) => {
        this.setState({ currentView: view, mobileMenuOpen: false });
    }

    handleReservar = (servicio) => {
        const { currentUser } = this.state;
        
        if (!currentUser) {
            if (confirm('Debes iniciar sesión como TURISTA para reservar.\n\n¿Quieres registrarte o iniciar sesión ahora?')) {
                this.setState({ showAuth: true, isRegistering: false });
            }
            return;
        }

        if (currentUser.tipo_usuario !== 'turista') {
            alert('Solo los usuarios registrados como TURISTA pueden hacer reservas.\n\nTu cuenta actual es de tipo: ' + currentUser.tipo_usuario + '\n\nPara reservar, debes crear una cuenta nueva como Turista.');
            return;
        }

        this.setState({
            showReservaModal: true,
            servicioAReservar: servicio,
            reservaFormData: {
                fecha_reserva: '',
                hora_reserva: '',
                numero_personas: 1,
                notas_turista: ''
            }
        });
    }

    handleEditarReserva = (reserva) => {
        this.setState({
            showReservaModal: true,
            servicioAReservar: { 
                id: reserva.servicio_id,
                nombre_servicio: reserva.nombre_servicio,
                precio_desde: reserva.precio_total / reserva.numero_personas
            },
            reservaFormData: {
                reserva_id: reserva.id,
                fecha_reserva: reserva.fecha_reserva,
                hora_reserva: reserva.hora_reserva || '',
                numero_personas: reserva.numero_personas,
                notas_turista: reserva.notas_turista || ''
            }
        });
    }

    handleCancelarReserva = async (reserva_id) => {
        if (!confirm('¿Estás seguro de cancelar esta reserva?')) return;

        try {
            const response = await fetch(`${API_URL}/reservas/actualizar_estado.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    reserva_id: reserva_id,
                    nuevo_estado: 'cancelada'
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Reserva cancelada exitosamente');
                this.loadMisReservas();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        }
    }

    handleCambiarEstadoReserva = async (reserva_id, nuevo_estado) => {
        const mensajes = {
            'confirmada': '¿Confirmar esta reserva?',
            'cancelada': '¿Cancelar esta reserva?',
            'completada': '¿Marcar esta reserva como completada?'
        };

        if (!confirm(mensajes[nuevo_estado])) return;

        try {
            const response = await fetch(`${API_URL}/reservas/actualizar_estado.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    reserva_id: reserva_id,
                    nuevo_estado: nuevo_estado
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Estado de reserva actualizado exitosamente');
                this.loadReservasRecibidas();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        }
    }

    handleCrearReserva = async () => {
        const { currentUser, servicioAReservar, reservaFormData } = this.state;

        if (!reservaFormData.fecha_reserva) {
            alert('Por favor selecciona una fecha');
            return;
        }

        try {
            this.setState({ loading: true });

            const esEdicion = reservaFormData.reserva_id ? true : false;

            if (esEdicion) {
                // Actualizar reserva existente
                const payload = {
                    reserva_id: reservaFormData.reserva_id,
                    fecha_reserva: reservaFormData.fecha_reserva,
                    hora_reserva: reservaFormData.hora_reserva || null,
                    numero_personas: reservaFormData.numero_personas,
                    precio_total: servicioAReservar.precio_desde * reservaFormData.numero_personas,
                    notas_turista: reservaFormData.notas_turista
                };

                const response = await fetch(`${API_URL}/reservas/actualizar.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Reserva actualizada exitosamente');
                    this.setState({ showReservaModal: false, servicioAReservar: null });
                    this.loadMisReservas();
                } else {
                    alert('Error: ' + data.message);
                }
            } else {
                // Crear nueva reserva
                const payload = {
                    servicio_id: servicioAReservar.id,
                    turista_id: currentUser.id,
                    fecha_reserva: reservaFormData.fecha_reserva,
                    hora_reserva: reservaFormData.hora_reserva || null,
                    numero_personas: reservaFormData.numero_personas,
                    precio_total: servicioAReservar.precio_desde * reservaFormData.numero_personas,
                    notas_turista: reservaFormData.notas_turista
                };

                const response = await fetch(`${API_URL}/reservas/crear.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Reserva creada exitosamente');
                    this.setState({ showReservaModal: false, servicioAReservar: null });
                    this.loadMisReservas();
                } else {
                    alert('Error: ' + data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleCreateService = async () => {
        const { currentUser, serviceFormData } = this.state;
        
        console.log('=== CREAR SERVICIO ===');
        console.log('Usuario actual:', currentUser);
        console.log('Datos del formulario:', serviceFormData);
        
        if (!serviceFormData.nombre_servicio || !serviceFormData.rnt) {
            alert('Por favor completa los campos obligatorios: Nombre y RNT');
            return;
        }

        try {
            this.setState({ loading: true });
            
            const roleInfo = this.userRoles.find(r => r.id === currentUser.role);
            console.log('Role info:', roleInfo);
            
            const payload = {
                usuario_id: currentUser.id,
                tipo_servicio: roleInfo.dbValue,
                nombre_servicio: serviceFormData.nombre_servicio,
                rnt: serviceFormData.rnt,
                descripcion: serviceFormData.descripcion || '',
                direccion: serviceFormData.direccion || '',
                telefono: serviceFormData.telefono || '',
                email: serviceFormData.email || '',
                horario_apertura: serviceFormData.horario_apertura || null,
                horario_cierre: serviceFormData.horario_cierre || null,
                precio_desde: serviceFormData.precio_desde || null,
                precio_hasta: serviceFormData.precio_hasta || null
            };

            console.log('Payload a enviar:', payload);
            
            const response = await fetch(`${API_URL}/servicios/crear.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                alert('Servicio registrado exitosamente!\n\nID del servicio: ' + (data.servicio_id || 'N/A'));
                this.setState({
                    showServiceForm: false,
                    serviceFormData: {
                        nombre_servicio: '',
                        rnt: '',
                        descripcion: '',
                        direccion: '',
                        telefono: '',
                        email: '',
                        horario_apertura: '',
                        horario_cierre: '',
                        precio_desde: '',
                        precio_hasta: ''
                    }
                });
                
                console.log('Recargando servicios...');
                await this.loadServicios();
                await this.loadMisServicios();
                console.log('Servicios recargados');
            } else {
                console.error('Error del servidor:', data.message);
                alert(data.message || 'Error al registrar servicio');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleAbrirEdicion = async (servicio) => {
        this.setState({
            showEditModal: true,
            servicioAEditar: { ...servicio },
            editModalTab: 'info'
        });
        await this.loadFotosServicio(servicio.id);
    }

    loadFotosServicio = async (servicio_id) => {
        try {
            const response = await fetch(`${API_URL}/servicios/fotos.php?servicio_id=${servicio_id}`);
            const data = await response.json();
            if (data.success) {
                this.setState({ fotosServicio: data.data });
            }
        } catch (error) {
            console.error('Error cargando fotos:', error);
        }
    }

    handleActualizarServicio = async () => {
        const { servicioAEditar, currentUser } = this.state;

        if (!servicioAEditar.nombre_servicio) {
            alert('El nombre del servicio es obligatorio');
            return;
        }

        try {
            this.setState({ loading: true });

            const payload = {
                id: servicioAEditar.id,
                usuario_id: currentUser.id,
                nombre_servicio: servicioAEditar.nombre_servicio,
                descripcion: servicioAEditar.descripcion || '',
                direccion: servicioAEditar.direccion || '',
                telefono: servicioAEditar.telefono || '',
                email: servicioAEditar.email || '',
                horario_apertura: servicioAEditar.horario_apertura || null,
                horario_cierre: servicioAEditar.horario_cierre || null,
                precio_desde: servicioAEditar.precio_desde || null,
                precio_hasta: servicioAEditar.precio_hasta || null
            };

            const response = await fetch(`${API_URL}/servicios/actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ Servicio actualizado exitosamente');
                this.setState({ showEditModal: false, servicioAEditar: null });
                this.loadMisServicios();
                this.loadServicios();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleAgregarFoto = async () => {
        const { servicioAEditar, nuevaFoto } = this.state;

        if (!nuevaFoto.url_foto) {
            alert('Por favor ingresa la URL de la foto');
            return;
        }

        try {
            this.setState({ loading: true });

            const payload = {
                servicio_id: servicioAEditar.id,
                url_foto: nuevaFoto.url_foto,
                descripcion: nuevaFoto.descripcion || null,
                es_principal: nuevaFoto.es_principal,
                orden: 0
            };

            const response = await fetch(`${API_URL}/servicios/agregar_foto.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ Foto agregada exitosamente');
                this.setState({
                    nuevaFoto: { url_foto: '', descripcion: '', es_principal: 0 }
                });
                await this.loadFotosServicio(servicioAEditar.id);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        } finally {
            this.setState({ loading: false });
        }
    }

    handleEliminarFoto = async (foto_id) => {
        if (!confirm('¿Estás seguro de eliminar esta foto?')) return;

        try {
            const response = await fetch(`${API_URL}/servicios/eliminar_foto.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ foto_id })
            });

            const data = await response.json();

            if (data.success) {
                alert('✅ Foto eliminada');
                await this.loadFotosServicio(this.state.servicioAEditar.id);
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión');
        }
    }

    renderContent() {
        const { currentView, currentUser } = this.state;

        switch(currentView) {
            case 'home':
                return this.renderHome();
            case 'about':
                return this.renderAbout();
            case 'gallery':
                return this.renderGallery();
            case 'reviews':
                return this.renderReviews();
            case 'dashboard':
                return currentUser ? this.renderDashboard() : this.renderHome();
            default:
                return this.renderHome();
        }
    }

    renderHome() {
        const { servicios, programas, currentUser } = this.state;
        
        return React.createElement('div', { className: 'space-y-12' }, [
            // Hero Section
            React.createElement('section', { key: 'hero', className: 'hero' }, [
                React.createElement('img', {
                    key: 'hero-img',
                    src: 'https://hotelcaciquereal.com/img/noticias/6.jpg',
                    alt: 'Catedral de Sal'
                }),
                React.createElement('div', { key: 'hero-overlay', className: 'hero-overlay' },
                    React.createElement('div', { className: 'hero-content' }, [
                        React.createElement('h2', { key: 'h2' }, 'Bienvenidos a Zipaquirá'),
                        React.createElement('p', { key: 'p' }, 'Descubre la magia de la Catedral de Sal'),
                        React.createElement('button', {
                            key: 'btn',
                            className: 'btn btn-primary',
                            onClick: () => this.setState({ showAuth: true })
                        }, 'Comienza tu experiencia')
                    ])
                )
            ]),
            
            // Dashboard Power BI
            React.createElement('section', { key: 'dashboard', className: 'section' }, [
                React.createElement('div', { key: 'dashboard-card', className: 'card', style: { textAlign: 'center', background: 'linear-gradient(135deg, #b19a71ff 0%, #D9BCB6 100%)', color: 'white', padding: '2rem' } }, [
                    React.createElement('i', { key: 'icon', className: 'fas fa-chart-line', style: { fontSize: '3rem', marginBottom: '1rem' } }),
                    React.createElement('h3', { key: 'h3', style: { color: 'white', marginBottom: '1rem' } }, '📊 Dashboard de Turismo Zipaquirá'),
                    React.createElement('p', { key: 'desc', style: { marginBottom: '1.5rem', fontSize: '1.1rem' } }, 
                        'Visualiza estadísticas y análisis del turismo en Zipaquirá con nuestro dashboard interactivo de Power BI'
                    ),
                    React.createElement('a', {
                        key: 'btn',
                        href: 'assets/docs/Dashboard Turismo Zipaquira.pbix',
                        download: 'Dashboard Turismo Zipaquira.pbix',
                        className: 'btn btn-primary',
                        style: { display: 'inline-block', textDecoration: 'none', backgroundColor: 'white', color: '#667eea', fontWeight: 'bold' }
                    }, '⬇️ Descargar Dashboard (.pbix)')
                ])
            ]),
            
            // Servicios Públicos
            React.createElement('section', { key: 'servicios', className: 'section' }, [
                React.createElement('h3', { key: 'h3', style: { textAlign: 'center', marginBottom: '2rem' } }, 
                    '🏨 Servicios Turísticos Disponibles'
                ),
                servicios.length === 0 ? 
                    React.createElement('p', { key: 'empty', style: { textAlign: 'center', color: '#666' } },
                        'Cargando servicios...'
                    ) :
                    React.createElement('div', { key: 'grid', className: 'cards-grid' },
                        servicios.slice(0, 9).map(servicio =>
                            React.createElement('div', { key: servicio.id, className: 'card' }, [
                                servicio.foto_principal && React.createElement('img', {
                                    key: 'img',
                                    src: servicio.foto_principal,
                                    alt: servicio.nombre_servicio,
                                    style: { width: '100%', height: '200px', objectFit: 'cover', borderRadius: '0.5rem', marginBottom: '1rem' },
                                    onError: (e) => { e.target.style.display = 'none'; }
                                }),
                                React.createElement('h4', { key: 'h4' }, servicio.nombre_servicio),
                                React.createElement('p', { key: 'tipo', style: { fontSize: '0.85rem', color: '#666', marginBottom: '0.5rem' } },
                                    `📍 ${servicio.tipo_servicio}`
                                ),
                                servicio.descripcion && React.createElement('p', { key: 'desc', style: { fontSize: '0.9rem', marginBottom: '1rem' } },
                                    servicio.descripcion.substring(0, 100) + '...'
                                ),
                                servicio.precio_desde && React.createElement('p', { key: 'precio', style: { fontWeight: 'bold', color: '#0066cc', marginBottom: '1rem' } },
                                    `Desde ${parseFloat(servicio.precio_desde).toLocaleString()} COP`
                                ),
                                React.createElement('button', {
                                    key: 'btn',
                                    className: 'btn btn-blue',
                                    style: { width: '100%' },
                                    onClick: () => this.handleReservar(servicio)
                                }, 'Reservar')
                            ])
                        )
                    )
            ]),

            // Programas Gubernamentales
            programas.length > 0 && React.createElement('section', { key: 'programas', className: 'section' }, [
                React.createElement('h3', { key: 'h3', style: { textAlign: 'center', marginBottom: '2rem' } }, 
                    '🏛️ Programas Gubernamentales'
                ),
                React.createElement('div', { key: 'grid', className: 'cards-grid' },
                    programas.slice(0, 3).map(programa =>
                        React.createElement('div', { key: programa.id, className: 'card' }, [
                            React.createElement('h4', { key: 'h4' }, programa.titulo),
                            React.createElement('p', { key: 'desc' }, programa.descripcion),
                            programa.fecha_inicio && React.createElement('p', { key: 'fecha', style: { fontSize: '0.85rem', color: '#666' } },
                                `📅 ${programa.fecha_inicio}`
                            )
                        ])
                    )
                )
            ]),
            
            // Roles Section - Solo mostrar si NO hay sesión iniciada
            !currentUser && React.createElement('section', { key: 'roles', className: 'section' }, [
                React.createElement('h3', { key: 'h3' }, 'Únete a nuestra comunidad turística'),
                React.createElement('div', { key: 'grid', className: 'cards-grid' },
                    this.userRoles.map(role =>
                        React.createElement('div', { 
                            key: role.id, 
                            className: 'card',
                            style: { cursor: 'pointer' },
                            onClick: () => {
                                this.setState({ 
                                    showAuth: true, 
                                    isRegistering: true,
                                    selectedRole: role.id 
                                });
                            }
                        }, [
                            React.createElement('i', { key: 'icon', className: `fas ${role.icon}` }),
                            React.createElement('p', { key: 'name' }, role.name),
                            React.createElement('button', { 
                                key: 'btn',
                                className: 'btn btn-blue',
                                style: { marginTop: '1rem', width: '100%' }
                            }, 'Registrarse')
                        ])
                    )
                )
            ])
        ]);
    }

    renderAbout() {
        return React.createElement('div', { className: 'space-y-8' }, [
            React.createElement('div', { key: 'about', className: 'card' }, [
                React.createElement('h2', { key: 'h2', className: 'text-blue' }, 'Sobre Zipaquirá'),
                React.createElement('div', { key: 'content', className: 'cards-grid' }, [
                    React.createElement('div', { key: 'img' },
                        React.createElement('img', {
                            src: 'https://zipaquiraturistica.com/src/img/ciudad-n/8_1.png',
                            alt: 'Zipaquirá',
                            style: { width: '100%', borderRadius: '1rem' }
                        })
                    ),
                    React.createElement('div', { key: 'text' }, [
                        React.createElement('p', { key: 'p1' }, 'Zipaquirá es un municipio colombiano ubicado en el departamento de Cundinamarca, a 49 km al norte de Bogotá. Es conocida mundialmente por albergar la Catedral de Sal, una majestuosa obra arquitectónica construida dentro de las minas de sal.'),
                        React.createElement('p', { key: 'p2' }, 'La ciudad cuenta con un rico patrimonio histórico y cultural, siendo declarada Monumento Nacional. Su centro histórico conserva la arquitectura colonial y es un lugar perfecto para caminar y disfrutar de su gastronomía.')
                    ])
                ])
            ])
        ]);
    }

    renderGallery() {
        const images = [
            { url: 'https://phantom-elmundo.unidadeditorial.es/444261db21f3160a62bb9636698dc403/resize/1200/f/jpg/assets/multimedia/imagenes/2021/09/03/16306829892598.jpg', title: 'Catedral de Sal - Vista Principal' },
            { url: 'https://zipaquiraturistica.com/dashboard-zipa-turis/storage/blog/yv4hvhYMHy1o0eE2PmxTl3XZSz2APmDRyFybaTUg.jpg', title: 'Plaza de la independencia' },
            { url: 'https://zipaquiraturistica.com/dashboard-zipa-turis/storage/blog/IiddATTAlEjVtsisOZbL3qkFKunWfkDilSaLeqme.jpg', title: 'Plaza Principal' },
            { url: 'https://www.semana.com/resizer/v2/J46VJRKIBZEYDM4O74FPAM76AE.jpg?auth=6426dc7b1222dc27a8be6b2c25f98cee28fbe9bad6348f8dbfd33aaab4424f11&smart=true&quality=75&width=1280&height=960', title: 'Arquitectura Colonial' }
        ];

        return React.createElement('div', { className: 'space-y-8' }, [
            React.createElement('h2', { key: 'h2', className: 'text-blue' }, 'Galería de Zipaquirá'),
            React.createElement('div', { key: 'grid', className: 'cards-grid' },
                images.map((img, index) =>
                    React.createElement('div', { key: index, className: 'card' }, [
                        React.createElement('img', {
                            key: 'img',
                            src: img.url,
                            alt: img.title,
                            style: { width: '100%', height: '250px', objectFit: 'cover', borderRadius: '0.5rem' }
                        }),
                        React.createElement('h4', { key: 'title', style: { marginTop: '1rem' } }, img.title)
                    ])
                )
            )
        ]);
    }

    renderReviews() {
        const { reviews, newReview, currentUser } = this.state;

        return React.createElement('div', { className: 'space-y-8' }, [
            React.createElement('h2', { key: 'h2', className: 'text-blue' }, 'Reseñas de Visitantes'),
            
            currentUser && React.createElement('div', { key: 'form', className: 'card' }, [
                React.createElement('h3', { key: 'h3' }, 'Deja tu reseña'),
                React.createElement('div', { key: 'rating', style: { marginBottom: '1rem' } }, [
                    React.createElement('label', { key: 'label' }, 'Calificación: '),
                    React.createElement('select', {
                        key: 'select',
                        className: 'input',
                        value: newReview.rating,
                        onChange: (e) => this.setState({
                            newReview: { ...newReview, rating: parseInt(e.target.value) }
                        })
                    }, [1, 2, 3, 4, 5].map(n =>
                        React.createElement('option', { key: n, value: n }, `${n} estrellas`)
                    ))
                ]),
                React.createElement('textarea', {
                    key: 'comment',
                    className: 'input',
                    rows: 4,
                    placeholder: 'Escribe tu comentario...',
                    value: newReview.comment,
                    onChange: (e) => this.setState({
                        newReview: { ...newReview, comment: e.target.value }
                    })
                }),
                React.createElement('button', {
                    key: 'btn',
                    className: 'btn btn-blue',
                    style: { marginTop: '1rem' },
                    onClick: this.handleSubmitReview
                }, 'Publicar Reseña')
            ]),

            React.createElement('div', { key: 'list', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } },
                reviews.map(review =>
                    React.createElement('div', { key: review.id, className: 'card' }, [
                        React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' } }, [
                            React.createElement('strong', { key: 'user' }, review.user),
                            React.createElement('span', { key: 'rating' }, '⭐'.repeat(review.rating))
                        ]),
                        React.createElement('p', { key: 'comment' }, review.comment),
                        React.createElement('small', { key: 'date', style: { color: '#666' } }, review.date)
                    ])
                )
            )
        ]);
    }

    handleSubmitReview = () => {
        const { newReview, currentUser, reviews } = this.state;
        
        if (!newReview.comment.trim()) {
            alert('Por favor escribe un comentario');
            return;
        }

        const roleInfo = this.userRoles.find(r => r.id === currentUser.role);
        const review = {
            id: reviews.length + 1,
            user: currentUser.name,
            role: roleInfo ? roleInfo.name : 'Usuario',
            rating: newReview.rating,
            comment: newReview.comment,
            date: new Date().toISOString().split('T')[0]
        };

        this.setState({
            reviews: [review, ...reviews],
            newReview: { rating: 5, comment: '' }
        });
    }

    renderDashboard() {
        const { currentUser } = this.state;

        if (currentUser.role === 'government') {
            return this.renderGovernmentDashboard();
        } else if (currentUser.role === 'tourist') {
            return this.renderTouristDashboard();
        } else if (['agency', 'operator', 'restaurant', 'hotel'].includes(currentUser.role)) {
            return this.renderServiceProviderDashboard();
        }
    }

    renderTouristDashboard() {
        const { misReservas } = this.state;

        return React.createElement('div', null, [
            React.createElement('h2', { key: 'h2', className: 'text-blue', style: { marginBottom: '2rem' } }, 
                '📋 Mis Reservas'
            ),
            
            React.createElement('div', { key: 'info', className: 'card', style: { background: '#e7f3ff', marginBottom: '2rem' } }, [
                React.createElement('p', { key: 'p', style: { margin: 0 } }, 
                    '💡 Explora los servicios disponibles en la página de Inicio y haz tus reservas.'
                )
            ]),

            React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', marginBottom: '1rem' } }, [
                React.createElement('h3', { key: 'h3' }, 'Historial de Reservas'),
                React.createElement('button', {
                    key: 'btn',
                    className: 'btn btn-blue',
                    onClick: this.loadMisReservas
                }, '🔄 Actualizar')
            ]),
            
            misReservas.length === 0 ?
                React.createElement('div', { key: 'empty', className: 'card', style: { textAlign: 'center', padding: '3rem' } }, [
                    React.createElement('i', { key: 'icon', className: 'fas fa-calendar-times', style: { fontSize: '3rem', marginBottom: '1rem', color: '#ccc' } }),
                    React.createElement('p', { key: 'p1', style: { fontSize: '1.1rem', marginBottom: '0.5rem' } },
                        'No tienes reservas aún'
                    ),
                    React.createElement('p', { key: 'p2', style: { fontSize: '0.9rem', color: '#666' } },
                        '¡Ve a Inicio y reserva un servicio!'
                    ),
                    React.createElement('button', {
                        key: 'btn',
                        className: 'btn btn-blue',
                        style: { marginTop: '1rem' },
                        onClick: () => this.setView('home')
                    }, '🏠 Ir a Inicio')
                ]) :
                React.createElement('div', { key: 'list', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } },
                    misReservas.map(reserva =>
                        React.createElement('div', { key: reserva.id, className: 'card' }, [
                            React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '1rem' } }, [
                                React.createElement('div', { key: 'info' }, [
                                    React.createElement('h4', { key: 'h4', style: { marginBottom: '0.5rem' } }, reserva.nombre_servicio),
                                    React.createElement('span', {
                                        key: 'estado',
                                        style: {
                                            background: reserva.estado === 'confirmada' ? '#28a745' : 
                                                       reserva.estado === 'cancelada' ? '#dc3545' :
                                                       reserva.estado === 'completada' ? '#0066cc' : '#ffc107',
                                            color: 'white',
                                            padding: '0.25rem 0.75rem',
                                            borderRadius: '1rem',
                                            fontSize: '0.85rem',
                                            fontWeight: 'bold'
                                        }
                                    }, reserva.estado.toUpperCase())
                                ]),
                                reserva.estado === 'pendiente' && React.createElement('div', { key: 'actions', style: { display: 'flex', gap: '0.5rem' } }, [
                                    React.createElement('button', {
                                        key: 'editar',
                                        className: 'btn btn-blue',
                                        style: { fontSize: '0.85rem', padding: '0.5rem 1rem' },
                                        onClick: () => this.handleEditarReserva(reserva)
                                    }, 'Editar'),
                                    React.createElement('button', {
                                        key: 'cancelar',
                                        className: 'btn',
                                        style: { fontSize: '0.85rem', padding: '0.5rem 1rem', background: '#dc3545', color: 'white' },
                                        onClick: () => this.handleCancelarReserva(reserva.id)
                                    }, 'Cancelar')
                                ])
                            ]),
                            React.createElement('div', { key: 'details', style: { fontSize: '0.9rem', color: '#666' } }, [
                                React.createElement('p', { key: 'fecha', style: { marginBottom: '0.5rem' } }, 
                                    `📅 ${reserva.fecha_reserva}${reserva.hora_reserva ? ' - ' + reserva.hora_reserva : ''}`
                                ),
                                React.createElement('p', { key: 'personas', style: { marginBottom: '0.5rem' } }, 
                                    `👥 ${reserva.numero_personas} persona(s)`
                                ),
                                reserva.precio_total && React.createElement('p', { key: 'precio', style: { marginBottom: '0.5rem', fontWeight: 'bold', color: '#0066cc' } }, 
                                    `💰 ${parseFloat(reserva.precio_total).toLocaleString()} COP`
                                ),
                                reserva.notas_turista && React.createElement('p', { key: 'notas', style: { marginTop: '1rem', padding: '0.75rem', background: '#f8f9fa', borderRadius: '0.5rem', fontSize: '0.85rem' } }, 
                                    `📝 Notas: ${reserva.notas_turista}`
                                )
                            ])
                        ])
                    )
                )
        ]);
    }

    renderServiceProviderDashboard() {
        const { currentUser, misServicios, showServiceForm, serviceFormData, reservasRecibidas } = this.state;
        const roleInfo = this.userRoles.find(r => r.id === currentUser.role);
        
        console.log('=== RENDER DASHBOARD PROVEEDOR ===');
        console.log('Usuario actual:', currentUser);
        console.log('Role info:', roleInfo);
        console.log('Mis servicios:', misServicios);
        console.log('Total servicios:', misServicios.length);

        return React.createElement('div', null, [
            React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' } }, [
                React.createElement('h2', { key: 'h2', className: 'text-blue' }, 
                    `Panel de ${roleInfo?.name}`
                ),
                React.createElement('button', {
                    key: 'btn',
                    className: 'btn btn-blue',
                    onClick: () => this.setState({ showServiceForm: !showServiceForm })
                }, showServiceForm ? 'Cancelar' : '+ Registrar Servicio')
            ]),

            showServiceForm && React.createElement('div', { key: 'form', className: 'card', style: { marginBottom: '2rem' } }, [
                React.createElement('h3', { key: 'h3', style: { marginBottom: '1rem' } }, 'Registrar Nuevo Servicio'),
                React.createElement('div', { key: 'form-grid', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } }, [
                    React.createElement('input', {
                        key: 'nombre',
                        type: 'text',
                        className: 'input',
                        placeholder: 'Nombre del servicio *',
                        value: serviceFormData.nombre_servicio,
                        onChange: (e) => this.setState({
                            serviceFormData: { ...serviceFormData, nombre_servicio: e.target.value }
                        })
                    }),
                    React.createElement('input', {
                        key: 'rnt',
                        type: 'text',
                        className: 'input',
                        placeholder: 'RNT (Registro Nacional de Turismo) *',
                        value: serviceFormData.rnt,
                        onChange: (e) => this.setState({
                            serviceFormData: { ...serviceFormData, rnt: e.target.value }
                        })
                    }),
                    React.createElement('textarea', {
                        key: 'descripcion',
                        className: 'input',
                        placeholder: 'Descripción del servicio',
                        rows: 3,
                        value: serviceFormData.descripcion,
                        onChange: (e) => this.setState({
                            serviceFormData: { ...serviceFormData, descripcion: e.target.value }
                        })
                    }),
                    React.createElement('input', {
                        key: 'direccion',
                        type: 'text',
                        className: 'input',
                        placeholder: 'Dirección',
                        value: serviceFormData.direccion,
                        onChange: (e) => this.setState({
                            serviceFormData: { ...serviceFormData, direccion: e.target.value }
                        })
                    }),
                    React.createElement('div', { key: 'contacto', style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } }, [
                        React.createElement('input', {
                            key: 'telefono',
                            type: 'text',
                            className: 'input',
                            placeholder: 'Teléfono',
                            value: serviceFormData.telefono,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, telefono: e.target.value }
                            })
                        }),
                        React.createElement('input', {
                            key: 'email',
                            type: 'email',
                            className: 'input',
                            placeholder: 'Email',
                            value: serviceFormData.email,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, email: e.target.value }
                            })
                        })
                    ]),
                    React.createElement('div', { key: 'horarios', style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } }, [
                        React.createElement('input', {
                            key: 'apertura',
                            type: 'time',
                            className: 'input',
                            placeholder: 'Horario Apertura',
                            value: serviceFormData.horario_apertura,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, horario_apertura: e.target.value }
                            })
                        }),
                        React.createElement('input', {
                            key: 'cierre',
                            type: 'time',
                            className: 'input',
                            placeholder: 'Horario Cierre',
                            value: serviceFormData.horario_cierre,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, horario_cierre: e.target.value }
                            })
                        })
                    ]),
                    React.createElement('div', { key: 'precios', style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } }, [
                        React.createElement('input', {
                            key: 'desde',
                            type: 'number',
                            className: 'input',
                            placeholder: 'Precio Desde (COP)',
                            value: serviceFormData.precio_desde,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, precio_desde: e.target.value }
                            })
                        }),
                        React.createElement('input', {
                            key: 'hasta',
                            type: 'number',
                            className: 'input',
                            placeholder: 'Precio Hasta (COP)',
                            value: serviceFormData.precio_hasta,
                            onChange: (e) => this.setState({
                                serviceFormData: { ...serviceFormData, precio_hasta: e.target.value }
                            })
                        })
                    ]),
                    React.createElement('button', {
                        key: 'submit',
                        className: 'btn btn-blue',
                        onClick: this.handleCreateService
                    }, '✅ Registrar Servicio')
                ])
            ]),
            
            React.createElement('div', { key: 'servicios', className: 'card', style: { marginBottom: '2rem' } }, [
                React.createElement('h3', { key: 'h3', style: { marginBottom: '1rem' } }, '📋 Mis Servicios Registrados'),
                misServicios.length === 0 ?
                    React.createElement('p', { key: 'empty', style: { textAlign: 'center', padding: '2rem', color: '#666' } },
                        'No tienes servicios registrados. Haz clic en "+ Registrar Servicio"'
                    ) :
                    React.createElement('div', { key: 'list', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } },
                        misServicios.map(servicio =>
                            React.createElement('div', { key: servicio.id, className: 'card', style: { background: '#f8f9fa' } }, [
                                React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', marginBottom: '1rem' } }, [
                                    React.createElement('div', { key: 'info' }, [
                                        React.createElement('h4', { key: 'h4', style: { marginBottom: '0.5rem' } }, servicio.nombre_servicio),
                                        React.createElement('p', { key: 'rnt', style: { fontSize: '0.9rem', color: '#666' } }, `RNT: ${servicio.rnt}`)
                                    ]),
                                    React.createElement('button', {
                                        key: 'btn',
                                        className: 'btn btn-blue',
                                        onClick: () => this.handleAbrirEdicion(servicio)
                                    }, '✏️ Editar')
                                ]),
                                servicio.descripcion && React.createElement('p', { key: 'desc', style: { fontSize: '0.9rem', marginBottom: '0.5rem' } },
                                    servicio.descripcion.substring(0, 150) + '...'
                                ),
                                React.createElement('div', { key: 'details', style: { fontSize: '0.85rem', color: '#666' } }, [
                                    servicio.direccion && React.createElement('p', { key: 'dir' }, `📍 ${servicio.direccion}`),
                                    servicio.precio_desde && React.createElement('p', { key: 'precio' }, 
                                        `💰 ${parseFloat(servicio.precio_desde).toLocaleString()} - ${parseFloat(servicio.precio_hasta).toLocaleString()} COP`
                                    )
                                ])
                            ])
                        )
                    )
            ]),

            React.createElement('div', { key: 'reservas', className: 'card' }, [
                React.createElement('h3', { key: 'h3', style: { marginBottom: '1rem' } }, '📅 Reservas Recibidas'),
                reservasRecibidas.length === 0 ?
                    React.createElement('p', { key: 'empty', style: { textAlign: 'center', padding: '2rem', color: '#666' } },
                        'No has recibido reservas aún'
                    ) :
                    React.createElement('div', { key: 'list', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } },
                        reservasRecibidas.map(reserva => {
                            const estadoColor = {
                                'pendiente': '#ffc107',
                                'confirmada': '#28a745',
                                'cancelada': '#dc3545',
                                'completada': '#17a2b8'
                            };
                            
                            return React.createElement('div', { 
                                key: reserva.id, 
                                className: 'card', 
                                style: { background: '#f8f9fa', borderLeft: `4px solid ${estadoColor[reserva.estado]}` }
                            }, [
                                React.createElement('div', { key: 'header', style: { display: 'flex', justifyContent: 'space-between', marginBottom: '1rem' } }, [
                                    React.createElement('div', { key: 'info' }, [
                                        React.createElement('h4', { key: 'h4', style: { marginBottom: '0.5rem' } }, reserva.nombre_servicio),
                                        React.createElement('p', { key: 'turista', style: { fontSize: '0.9rem', color: '#666' } }, 
                                            `Cliente: ${reserva.nombre_turista}`
                                        )
                                    ]),
                                    React.createElement('span', {
                                        key: 'estado',
                                        style: {
                                            padding: '0.25rem 0.75rem',
                                            borderRadius: '1rem',
                                            fontSize: '0.85rem',
                                            fontWeight: 'bold',
                                            background: estadoColor[reserva.estado],
                                            color: 'white'
                                        }
                                    }, reserva.estado.toUpperCase())
                                ]),
                                React.createElement('div', { key: 'details', style: { fontSize: '0.9rem', marginBottom: '1rem' } }, [
                                    React.createElement('p', { key: 'fecha' }, `📅 Fecha: ${reserva.fecha_reserva}${reserva.hora_reserva ? ' a las ' + reserva.hora_reserva : ''}`),
                                    React.createElement('p', { key: 'personas' }, `👥 Personas: ${reserva.numero_personas}`),
                                    React.createElement('p', { key: 'precio' }, `💰 Total: ${parseFloat(reserva.precio_total).toLocaleString()} COP`),
                                    reserva.notas_turista && React.createElement('p', { key: 'notas', style: { fontStyle: 'italic', color: '#666' } }, 
                                        `Notas: ${reserva.notas_turista}`
                                    )
                                ]),
                                reserva.estado === 'pendiente' && React.createElement('div', { key: 'actions', style: { display: 'flex', gap: '0.5rem' } }, [
                                    React.createElement('button', {
                                        key: 'confirmar',
                                        className: 'btn',
                                        style: { background: '#28a745', color: 'white', flex: 1 },
                                        onClick: () => this.handleCambiarEstadoReserva(reserva.id, 'confirmada')
                                    }, '✅ Confirmar'),
                                    React.createElement('button', {
                                        key: 'cancelar',
                                        className: 'btn',
                                        style: { background: '#dc3545', color: 'white', flex: 1 },
                                        onClick: () => this.handleCambiarEstadoReserva(reserva.id, 'cancelada')
                                    }, '❌ Cancelar')
                                ]),
                                reserva.estado === 'confirmada' && React.createElement('div', { key: 'actions', style: { display: 'flex', gap: '0.5rem' } }, [
                                    React.createElement('button', {
                                        key: 'completar',
                                        className: 'btn',
                                        style: { background: '#17a2b8', color: 'white', flex: 1 },
                                        onClick: () => this.handleCambiarEstadoReserva(reserva.id, 'completada')
                                    }, '✔️ Marcar como Completada')
                                ])
                            ]);
                        })
                    )
            ])
        ]);
    }

    renderGovernmentDashboard() {
        const { programas } = this.state;

        return React.createElement('div', null, [
            React.createElement('h2', { key: 'h2', className: 'text-blue' }, 
                'Panel Gubernamental'
            ),
            React.createElement('div', { key: 'programas', className: 'card' }, [
                React.createElement('h3', { key: 'h3' }, '🏛️ Programas Publicados'),
                React.createElement('p', { key: 'p' }, `Total: ${programas.length} programas`)
            ])
        ]);
    }

    renderAuthModal() {
        const { showAuth, selectedRole, isRegistering, authData, loading } = this.state;

        if (!showAuth) return null;

        return React.createElement('div', { className: 'modal' },
            React.createElement('div', { className: 'modal-content', style: { maxWidth: '500px' } }, [
                React.createElement('div', { key: 'header', className: 'modal-header' }, [
                    React.createElement('h2', { key: 'h2' }, isRegistering ? 'Registro' : 'Iniciar Sesión'),
                    React.createElement('button', {
                        key: 'close',
                        className: 'close-btn',
                        onClick: () => this.setState({ showAuth: false })
                    }, '×')
                ]),

                React.createElement('div', { key: 'body', style: { padding: '1.5rem' } }, [
                    isRegistering && React.createElement('div', { key: 'role-info', className: 'card', style: { background: '#f0f0f0', marginBottom: '1rem' } },
                        React.createElement('p', { style: { margin: 0 } },
                            `Registrándote como: ${this.userRoles.find(r => r.id === selectedRole)?.name}`
                        )
                    ),

                    React.createElement('div', { key: 'form', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } }, [
                        isRegistering && React.createElement('input', {
                            key: 'nombre',
                            type: 'text',
                            className: 'input',
                            placeholder: 'Nombre completo *',
                            value: authData.nombre_completo,
                            onChange: (e) => this.setState({
                                authData: { ...authData, nombre_completo: e.target.value }
                            })
                        }),

                        React.createElement('input', {
                            key: 'email',
                            type: 'email',
                            className: 'input',
                            placeholder: 'Email *',
                            value: authData.email,
                            onChange: (e) => this.setState({
                                authData: { ...authData, email: e.target.value }
                            })
                        }),

                        React.createElement('input', {
                            key: 'password',
                            type: 'password',
                            className: 'input',
                            placeholder: 'Contraseña *',
                            value: authData.password,
                            onChange: (e) => this.setState({
                                authData: { ...authData, password: e.target.value }
                            })
                        }),

                        isRegistering && React.createElement('input', {
                            key: 'confirmPassword',
                            type: 'password',
                            className: 'input',
                            placeholder: 'Confirmar contraseña *',
                            value: authData.confirmPassword,
                            onChange: (e) => this.setState({
                                authData: { ...authData, confirmPassword: e.target.value }
                            })
                        }),

                        isRegistering && React.createElement('div', { key: 'checks', style: { display: 'flex', flexDirection: 'column', gap: '0.5rem' } }, [
                            React.createElement('label', { key: 'terminos', style: { display: 'flex', alignItems: 'center', gap: '0.5rem' } }, [
                                React.createElement('input', {
                                    key: 'check',
                                    type: 'checkbox',
                                    checked: authData.aceptacion_terminos,
                                    onChange: (e) => this.setState({
                                        authData: { ...authData, aceptacion_terminos: e.target.checked }
                                    })
                                }),
                                React.createElement('span', { key: 'text' }, 'Acepto términos y condiciones')
                            ]),
                            React.createElement('label', { key: 'habeas', style: { display: 'flex', alignItems: 'center', gap: '0.5rem' } }, [
                                React.createElement('input', {
                                    key: 'check',
                                    type: 'checkbox',
                                    checked: authData.aceptacion_habeas_data,
                                    onChange: (e) => this.setState({
                                        authData: { ...authData, aceptacion_habeas_data: e.target.checked }
                                    })
                                }),
                                React.createElement('span', { key: 'text' }, 'Acepto política de Habeas Data')
                            ])
                        ]),

                        React.createElement('button', {
                            key: 'submit',
                            className: 'btn btn-blue',
                            onClick: isRegistering ? this.handleRegister : this.handleLogin,
                            disabled: loading
                        }, loading ? 'Procesando...' : (isRegistering ? 'Registrarse' : 'Iniciar Sesión')),

                        React.createElement('button', {
                            key: 'toggle',
                            className: 'btn-link',
                            onClick: () => this.setState({ isRegistering: !isRegistering })
                        }, isRegistering ? '¿Ya tienes cuenta? Inicia sesión' : '¿No tienes cuenta? Regístrate')
                    ])
                ])
            ])
        );
    }

    render() {
        const { currentUser, mobileMenuOpen } = this.state;

        return React.createElement('div', null, [
            // Header
            React.createElement('header', { key: 'header' },
                React.createElement('div', { className: 'container' },
                    React.createElement('div', { className: 'header-content' }, [
                        React.createElement('div', { key: 'logo', className: 'logo' }, [
                            React.createElement('i', { key: 'icon', className: 'fas fa-map-marked-alt', style: { fontSize: '2rem' } }),
                            React.createElement('div', { key: 'text' }, [
                                React.createElement('h1', { key: 'h1' }, 'Zipaquirá Turística'),
                                React.createElement('p', { key: 'p' }, 'Ciudad de la Sal')
                            ])
                        ]),
                        React.createElement('nav', { key: 'nav' }, [
                            React.createElement('button', { key: 'home', onClick: () => this.setView('home') }, 'Inicio'),
                            React.createElement('button', { key: 'about', onClick: () => this.setView('about') }, 'Sobre Zipaquirá'),
                            React.createElement('button', { key: 'gallery', onClick: () => this.setView('gallery') }, 'Galería'),
                            React.createElement('button', { key: 'reviews', onClick: () => this.setView('reviews') }, 'Reseñas'),
                            currentUser && React.createElement('button', { key: 'dashboard', onClick: () => this.setView('dashboard') }, 
                                currentUser.tipo_usuario === 'turista' ? 'Mis Reservas' : 'Panel'
                            )
                        ]),
                        React.createElement('div', { key: 'actions' },
                            currentUser ?
                                React.createElement('button', {
                                    className: 'btn btn-logout',
                                    onClick: this.handleLogout
                                }, `Salir (${currentUser.name})`) :
                                React.createElement('button', {
                                    className: 'btn btn-primary',
                                    onClick: () => this.setState({ showAuth: true })
                                }, 'Acceder')
                        )
                    ])
                )
            ),
            
            // Main Content
            React.createElement('main', { key: 'main', className: 'container' },
                this.renderContent()
            ),
            
            // Footer
            React.createElement('footer', { key: 'footer' }, [
                React.createElement('p', { key: 'p1' }, '© 2025 Zipaquirá Turística'),
                React.createElement('small', { key: 'small' }, 'Ley 1581 de 2012 - Protección de Datos')
            ]),
            
            // Modales
            this.renderAuthModal(),
            this.renderReservaModal(),
            this.renderEditModal()
        ]);
    }

    renderReservaModal() {
        const { showReservaModal, servicioAReservar, reservaFormData, loading } = this.state;

        if (!showReservaModal || !servicioAReservar) return null;

        return React.createElement('div', { className: 'modal' },
            React.createElement('div', { className: 'modal-content', style: { maxWidth: '500px' } }, [
                React.createElement('div', { key: 'header', className: 'modal-header' }, [
                    React.createElement('h2', { key: 'h2' }, 
                        reservaFormData.reserva_id ? 'Editar Reserva' : 'Reservar Servicio'
                    ),
                    React.createElement('button', {
                        key: 'close',
                        className: 'close-btn',
                        onClick: () => this.setState({ showReservaModal: false, servicioAReservar: null })
                    }, '×')
                ]),

                React.createElement('div', { key: 'body', style: { padding: '1.5rem' } }, [
                    React.createElement('div', { key: 'info', style: { background: '#f8f9fa', padding: '1rem', borderRadius: '0.5rem', marginBottom: '1.5rem' } }, [
                        React.createElement('h3', { key: 'h3', style: { marginBottom: '0.5rem' } }, servicioAReservar.nombre_servicio),
                        servicioAReservar.precio_desde && React.createElement('p', { key: 'precio', style: { fontSize: '0.9rem', color: '#666' } },
                            `Precio: ${parseFloat(servicioAReservar.precio_desde).toLocaleString()} COP por persona`
                        )
                    ]),

                    React.createElement('div', { key: 'form', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } }, [
                        React.createElement('div', { key: 'fecha' }, [
                            React.createElement('label', { key: 'label', style: { display: 'block', marginBottom: '0.5rem', fontWeight: 'bold' } }, 
                                'Fecha de Reserva *'
                            ),
                            React.createElement('input', {
                                key: 'input',
                                type: 'date',
                                className: 'input',
                                min: new Date().toISOString().split('T')[0],
                                value: reservaFormData.fecha_reserva,
                                onChange: (e) => this.setState({
                                    reservaFormData: { ...reservaFormData, fecha_reserva: e.target.value }
                                })
                            })
                        ]),

                        React.createElement('div', { key: 'hora' }, [
                            React.createElement('label', { key: 'label', style: { display: 'block', marginBottom: '0.5rem', fontWeight: 'bold' } }, 
                                'Hora (opcional)'
                            ),
                            React.createElement('input', {
                                key: 'input',
                                type: 'time',
                                className: 'input',
                                value: reservaFormData.hora_reserva,
                                onChange: (e) => this.setState({
                                    reservaFormData: { ...reservaFormData, hora_reserva: e.target.value }
                                })
                            })
                        ]),

                        React.createElement('div', { key: 'personas' }, [
                            React.createElement('label', { key: 'label', style: { display: 'block', marginBottom: '0.5rem', fontWeight: 'bold' } }, 
                                'Número de Personas'
                            ),
                            React.createElement('input', {
                                key: 'input',
                                type: 'number',
                                className: 'input',
                                min: 1,
                                value: reservaFormData.numero_personas,
                                onChange: (e) => this.setState({
                                    reservaFormData: { ...reservaFormData, numero_personas: parseInt(e.target.value) }
                                })
                            })
                        ]),

                        React.createElement('div', { key: 'notas' }, [
                            React.createElement('label', { key: 'label', style: { display: 'block', marginBottom: '0.5rem', fontWeight: 'bold' } }, 
                                'Notas o Solicitudes Especiales'
                            ),
                            React.createElement('textarea', {
                                key: 'input',
                                className: 'input',
                                rows: 3,
                                placeholder: 'Ej: Preferencias, alergias...',
                                value: reservaFormData.notas_turista,
                                onChange: (e) => this.setState({
                                    reservaFormData: { ...reservaFormData, notas_turista: e.target.value }
                                })
                            })
                        ]),

                        servicioAReservar.precio_desde && React.createElement('div', { key: 'total', style: { background: '#e7f3ff', padding: '1rem', borderRadius: '0.5rem' } }, [
                            React.createElement('p', { key: 'label', style: { fontWeight: 'bold', marginBottom: '0.5rem' } }, 'Total Estimado:'),
                            React.createElement('p', { key: 'valor', style: { fontSize: '1.5rem', color: '#0066cc', fontWeight: 'bold' } },
                                `${(parseFloat(servicioAReservar.precio_desde) * reservaFormData.numero_personas).toLocaleString()} COP`
                            )
                        ])
                    ])
                ]),

                React.createElement('div', { key: 'footer', style: { padding: '1rem 1.5rem', display: 'flex', flexDirection: 'column', gap: '0.5rem' } }, [
                    React.createElement('button', {
                        key: 'submit',
                        className: 'btn btn-blue',
                        style: { width: '100%' },
                        onClick: this.handleCrearReserva,
                        disabled: loading || !reservaFormData.fecha_reserva
                    }, loading ? 'Procesando...' : '✅ Confirmar Reserva'),
                    
                    React.createElement('button', {
                        key: 'cancel',
                        className: 'btn-link',
                        style: { width: '100%' },
                        onClick: () => this.setState({ showReservaModal: false })
                    }, 'Cancelar')
                ])
            ])
        );
    }

    renderEditModal() {
        const { showEditModal, servicioAEditar, fotosServicio, nuevaFoto, editModalTab, loading } = this.state;

        if (!showEditModal || !servicioAEditar) return null;

        return React.createElement('div', { className: 'modal' },
            React.createElement('div', { className: 'modal-content', style: { maxWidth: '800px', maxHeight: '90vh', overflowY: 'auto' } }, [
                React.createElement('div', { key: 'header', className: 'modal-header' }, [
                    React.createElement('h2', { key: 'h2' }, '✏️ Editar Servicio'),
                    React.createElement('button', {
                        key: 'close',
                        className: 'close-btn',
                        onClick: () => this.setState({ showEditModal: false, servicioAEditar: null })
                    }, '×')
                ]),

                React.createElement('div', { key: 'tabs', style: { display: 'flex', borderBottom: '2px solid #e0e0e0', padding: '0 1.5rem' } }, [
                    React.createElement('button', {
                        key: 'tab-info',
                        onClick: () => this.setState({ editModalTab: 'info' }),
                        style: {
                            padding: '1rem 2rem',
                            background: 'none',
                            border: 'none',
                            borderBottom: editModalTab === 'info' ? '3px solid #0066cc' : 'none',
                            color: editModalTab === 'info' ? '#0066cc' : '#666',
                            fontWeight: editModalTab === 'info' ? 'bold' : 'normal',
                            cursor: 'pointer'
                        }
                    }, '📝 Información'),
                    React.createElement('button', {
                        key: 'tab-fotos',
                        onClick: () => this.setState({ editModalTab: 'fotos' }),
                        style: {
                            padding: '1rem 2rem',
                            background: 'none',
                            border: 'none',
                            borderBottom: editModalTab === 'fotos' ? '3px solid #0066cc' : 'none',
                            color: editModalTab === 'fotos' ? '#0066cc' : '#666',
                            fontWeight: editModalTab === 'fotos' ? 'bold' : 'normal',
                            cursor: 'pointer'
                        }
                    }, `📷 Fotografías (${fotosServicio.length})`)
                ]),

                React.createElement('div', { key: 'body', style: { padding: '1.5rem' } }, [
                    editModalTab === 'info' ? this.renderEditInfoTab() : this.renderEditFotosTab()
                ]),

                React.createElement('div', { key: 'footer', style: { padding: '1rem 1.5rem', display: 'flex', flexDirection: 'column', gap: '0.5rem' } }, [
                    editModalTab === 'info' && React.createElement('button', {
                        key: 'save',
                        className: 'btn btn-blue',
                        style: { width: '100%' },
                        onClick: this.handleActualizarServicio,
                        disabled: loading
                    }, loading ? 'Guardando...' : '💾 Guardar Cambios'),
                    
                    React.createElement('button', {
                        key: 'cancel',
                        className: 'btn-link',
                        style: { width: '100%' },
                        onClick: () => this.setState({ showEditModal: false })
                    }, 'Cerrar')
                ])
            ])
        );
    }

    renderEditInfoTab() {
        const { servicioAEditar } = this.state;

        return React.createElement('div', { style: { display: 'flex', flexDirection: 'column', gap: '1rem' } }, [
            React.createElement('input', {
                key: 'nombre',
                type: 'text',
                className: 'input',
                placeholder: 'Nombre del servicio *',
                value: servicioAEditar.nombre_servicio,
                onChange: (e) => this.setState({
                    servicioAEditar: { ...servicioAEditar, nombre_servicio: e.target.value }
                })
            }),
            React.createElement('textarea', {
                key: 'descripcion',
                className: 'input',
                rows: 4,
                placeholder: 'Descripción',
                value: servicioAEditar.descripcion || '',
                onChange: (e) => this.setState({
                    servicioAEditar: { ...servicioAEditar, descripcion: e.target.value }
                })
            }),
            React.createElement('input', {
                key: 'direccion',
                type: 'text',
                className: 'input',
                placeholder: 'Dirección',
                value: servicioAEditar.direccion || '',
                onChange: (e) => this.setState({
                    servicioAEditar: { ...servicioAEditar, direccion: e.target.value }
                })
            }),
            React.createElement('div', { key: 'contacto', style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } }, [
                React.createElement('input', {
                    key: 'telefono',
                    type: 'text',
                    className: 'input',
                    placeholder: 'Teléfono',
                    value: servicioAEditar.telefono || '',
                    onChange: (e) => this.setState({
                        servicioAEditar: { ...servicioAEditar, telefono: e.target.value }
                    })
                }),
                React.createElement('input', {
                    key: 'email',
                    type: 'email',
                    className: 'input',
                    placeholder: 'Email',
                    value: servicioAEditar.email || '',
                    onChange: (e) => this.setState({
                        servicioAEditar: { ...servicioAEditar, email: e.target.value }
                    })
                })
            ]),
            React.createElement('div', { key: 'precios', style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' } }, [
                React.createElement('input', {
                    key: 'desde',
                    type: 'number',
                    className: 'input',
                    placeholder: 'Precio Desde',
                    value: servicioAEditar.precio_desde || '',
                    onChange: (e) => this.setState({
                        servicioAEditar: { ...servicioAEditar, precio_desde: e.target.value }
                    })
                }),
                React.createElement('input', {
                    key: 'hasta',
                    type: 'number',
                    className: 'input',
                    placeholder: 'Precio Hasta',
                    value: servicioAEditar.precio_hasta || '',
                    onChange: (e) => this.setState({
                        servicioAEditar: { ...servicioAEditar, precio_hasta: e.target.value }
                    })
                })
            ])
        ]);
    }

    renderEditFotosTab() {
        const { fotosServicio, nuevaFoto, loading } = this.state;

        return React.createElement('div', null, [
            React.createElement('div', { key: 'add-form', className: 'card', style: { background: '#f8f9fa', marginBottom: '1.5rem' } }, [
                React.createElement('h4', { key: 'h4', style: { marginBottom: '1rem' } }, 'Agregar Nueva Foto'),
                React.createElement('div', { key: 'help', style: { background: '#e7f3ff', padding: '0.75rem', borderRadius: '0.5rem', marginBottom: '1rem', fontSize: '0.9rem' } }, [
                    React.createElement('p', { key: 'p1', style: { margin: 0, marginBottom: '0.5rem', fontWeight: 'bold' } }, 'Cómo agregar fotos:'),
                    React.createElement('p', { key: 'p2', style: { margin: 0, marginBottom: '0.25rem' } }, '1. Sube tu imagen a Imgur.com (gratis, sin registro)'),
                    React.createElement('p', { key: 'p3', style: { margin: 0, marginBottom: '0.25rem' } }, '2. Copia el "Direct Link"'),
                    React.createElement('p', { key: 'p4', style: { margin: 0 } }, '3. Pégalo en el campo de abajo')
                ]),
                React.createElement('div', { key: 'form', style: { display: 'flex', flexDirection: 'column', gap: '1rem' } }, [
                    React.createElement('input', {
                        key: 'url',
                        type: 'text',
                        className: 'input',
                        placeholder: 'https://i.imgur.com/ejemplo.jpg',
                        value: nuevaFoto.url_foto,
                        onChange: (e) => this.setState({
                            nuevaFoto: { ...nuevaFoto, url_foto: e.target.value }
                        })
                    }),
                    React.createElement('input', {
                        key: 'desc',
                        type: 'text',
                        className: 'input',
                        placeholder: 'Descripción (opcional)',
                        value: nuevaFoto.descripcion,
                        onChange: (e) => this.setState({
                            nuevaFoto: { ...nuevaFoto, descripcion: e.target.value }
                        })
                    }),
                    React.createElement('label', { key: 'principal', style: { display: 'flex', alignItems: 'center', gap: '0.5rem' } }, [
                        React.createElement('input', {
                            key: 'checkbox',
                            type: 'checkbox',
                            checked: nuevaFoto.es_principal === 1,
                            onChange: (e) => this.setState({
                                nuevaFoto: { ...nuevaFoto, es_principal: e.target.checked ? 1 : 0 }
                            })
                        }),
                        React.createElement('span', { key: 'text' }, 'Marcar como foto principal')
                    ]),
                    React.createElement('button', {
                        key: 'btn',
                        className: 'btn btn-blue',
                        onClick: this.handleAgregarFoto,
                        disabled: loading || !nuevaFoto.url_foto
                    }, loading ? 'Agregando...' : '✅ Agregar Foto')
                ])
            ]),

            React.createElement('div', { key: 'list' }, [
                React.createElement('h4', { key: 'h4', style: { marginBottom: '1rem' } }, 
                    `📷 Fotos del Servicio (${fotosServicio.length})`
                ),
                fotosServicio.length === 0 ?
                    React.createElement('p', { key: 'empty', style: { textAlign: 'center', color: '#666', padding: '2rem' } },
                        'No hay fotos. Agrega la primera foto arriba.'
                    ) :
                    React.createElement('div', { key: 'grid', style: { display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: '1rem' } },
                        fotosServicio.map(foto =>
                            React.createElement('div', { 
                                key: foto.id,
                                className: 'card',
                                style: { padding: '0.5rem', position: 'relative' }
                            }, [
                                foto.es_principal === 1 && React.createElement('span', {
                                    key: 'badge',
                                    style: {
                                        position: 'absolute',
                                        top: '0.5rem',
                                        left: '0.5rem',
                                        background: '#28a745',
                                        color: 'white',
                                        padding: '0.25rem 0.5rem',
                                        borderRadius: '0.25rem',
                                        fontSize: '0.75rem',
                                        zIndex: 1
                                    }
                                }, '⭐ Principal'),
                                React.createElement('img', {
                                    key: 'img',
                                    src: foto.url_foto,
                                    alt: foto.descripcion || 'Foto',
                                    style: { 
                                        width: '100%', 
                                        height: '150px', 
                                        objectFit: 'cover', 
                                        borderRadius: '0.5rem',
                                        marginBottom: '0.5rem'
                                    },
                                    onError: (e) => {
                                        e.target.src = 'https://via.placeholder.com/200x150?text=Error';
                                    }
                                }),
                                foto.descripcion && React.createElement('p', {
                                    key: 'desc',
                                    style: { fontSize: '0.85rem', color: '#666', marginBottom: '0.5rem' }
                                }, foto.descripcion),
                                React.createElement('button', {
                                    key: 'delete',
                                    className: 'btn',
                                    style: { 
                                        width: '100%', 
                                        padding: '0.5rem',
                                        background: '#dc3545',
                                        color: 'white',
                                        fontSize: '0.85rem'
                                    },
                                    onClick: () => this.handleEliminarFoto(foto.id)
                                }, '🗑️ Eliminar')
                            ])
                        )
                    )
            ])
        ]);
    }
}

// Renderizar la aplicación
const root = ReactDOM.createRoot(document.getElementById('app'));
root.render(React.createElement(ZipaquiraTuristicaApp));
