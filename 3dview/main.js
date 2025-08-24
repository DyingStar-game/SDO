import * as THREE from 'three';
import CameraControls from './dist/camera-controls.module.js';
CameraControls.install( { THREE: THREE } );

const width = window.innerWidth;
const height = window.innerHeight;
const clock = new THREE.Clock();
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera( 60, width / height, 0.01, 100000000);
camera.position.set( -4000000, 0, 5 );
const renderer = new THREE.WebGLRenderer();
renderer.setSize( width, height );
document.body.appendChild( renderer.domElement );

const cameraControls = new CameraControls( camera, renderer.domElement );

async function getServerJSON() {
    const response = await fetch("servers.json");
    const json = await response.json();
    return json
    // console.log(json);
}

var pServers = getServerJSON();
pServers.then((servers) => {
    var newcolor = new THREE.Color( 0xffffff );
    var x = {};
    var y = {};
    var z = {};
    var separator = 50000;
    for (var key in servers) {
        x[servers[key].x_start] = 1;
        y[servers[key].y_start] = 1;
        z[servers[key].z_start] = 1;
    }

    for (var key in servers) {
        if (servers[key].is_free == 0)
        {
            var x_separator = 0;
            var y_separator = 0;
            var z_separator = 0;
            for (var x_position in x) {
                if (x_position <= servers[key].x_start) {
                    x_separator++;
                }
            }
            for (var y_position in y) {
                if (y_position <= servers[key].y_start) {
                    y_separator++;
                }
            }
            for (var z_position in z) {
                if (z_position <= servers[key].z_start) {
                    z_separator++;
                }
            }
            var generatedColor = newcolor.setHex(Math.random() * 0xffffff);
            
            var mesh = new THREE.Mesh(
                new THREE.BoxGeometry(
                    servers[key].x_size,
                    servers[key].y_size,
                    servers[key].z_size,
                ),
                new THREE.MeshBasicMaterial( { color: generatedColor, wireframe: true } )
            );
            console.log('%c' + servers[key].name, 'color: #' + generatedColor.getHexString());

            mesh.position.set(
                servers[key].x_start + (servers[key].x_size / 2) + (x_separator * separator),
                servers[key].y_start + (servers[key].y_size / 2) + (y_separator * separator),
                servers[key].z_start + (servers[key].z_size / 2) + (z_separator * separator)
            );
            scene.add(mesh);
        }
    }
});


const gridHelper = new THREE.GridHelper( 50, 50 );
gridHelper.position.y = - 1;
scene.add( gridHelper );

renderer.render( scene, camera );

( function anim () {

	const delta = clock.getDelta();
	const elapsed = clock.getElapsedTime();
	const updated = cameraControls.update( delta );

	// if ( elapsed > 30 ) { return; }

	requestAnimationFrame( anim );

	if ( updated ) {

		renderer.render( scene, camera );
		console.log( 'rendered' );

	}

} )();

// make variable available to browser console
globalThis.THREE = THREE;
globalThis.camera = camera;
globalThis.cameraControls = cameraControls;
