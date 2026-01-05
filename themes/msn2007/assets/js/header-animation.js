(function() {
    const config = {
        "shape": "wave",
        "count": 180,
        "size": 0.25,
        "linkDist": 12,
        "speed": 0.3,
        "spread": 80,
        "zoom": 3,
        "particleColor": "#003366", // Dark blue particles
        "lineColor": "#003366",     // Dark blue lines
        "bgColor": "#f1c40f"        // Yellow background
    };
    const container = document.getElementById('canvas-bg');
    if (!container) return; // Exit if container doesn't exist

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, 1, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

    scene.background = new THREE.Color(config.bgColor);
    container.appendChild(renderer.domElement);
    camera.position.z = 400 / config.zoom;

    const particleGroup = new THREE.Group();
    scene.add(particleGroup);

    let particlesData = [];
    let pointCloud;
    let linesMesh;

    function init() {
        const geometry = new THREE.BufferGeometry();
        const pPositions = [];
        const r = config.spread;

        for (let i = 0; i < config.count; i++) {
            let x = (Math.random() - 0.5) * 2 * r * 2.5;
            let z = (Math.random() - 0.5) * 2 * r;
            let y = Math.sin(x * 0.01) * 15;
            pPositions.push(x, y, z);
            particlesData.push({
                velocity: new THREE.Vector3(-1+Math.random()*2, -1+Math.random()*2, -1+Math.random()*2),
                originalX: x, originalZ: z
            });
        }

        geometry.setAttribute('position', new THREE.Float32BufferAttribute(pPositions, 3));
        const pMaterial = new THREE.PointsMaterial({ color: config.particleColor, size: config.size, transparent: true, opacity: 0.6 });
        pointCloud = new THREE.Points(geometry, pMaterial);
        particleGroup.add(pointCloud);

        const segments = config.count * config.count;
        const lGeometry = new THREE.BufferGeometry();
        lGeometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array(segments * 3), 3).setUsage(THREE.DynamicDrawUsage));
        lGeometry.setAttribute('color', new THREE.BufferAttribute(new Float32Array(segments * 3), 3).setUsage(THREE.DynamicDrawUsage));
        const lMaterial = new THREE.LineBasicMaterial({ vertexColors: true, transparent: true, opacity: 0.15 });
        linesMesh = new THREE.LineSegments(lGeometry, lMaterial);
        particleGroup.add(linesMesh);
        particleGroup.rotation.set(0.3, 0, 0);
    }

    function animate() {
        requestAnimationFrame(animate);
        let vertexpos = 0;
        let colorpos = 0;
        let numConnected = 0;
        const positions = pointCloud.geometry.attributes.position.array;
        const colors = linesMesh.geometry.attributes.color.array;
        const linePositions = linesMesh.geometry.attributes.position.array;
        const lineCol = new THREE.Color(config.lineColor);
        const time = Date.now() * 0.001 * config.speed * 2;

        for (let i = 0; i < config.count; i++) {
            const data = particlesData[i];
            data.originalX += data.velocity.x * config.speed * 0.2;
            data.originalZ += data.velocity.z * config.speed * 0.2;
            const limit = config.spread * 2.5;
            if (Math.abs(data.originalX) > limit) data.velocity.x *= -1;
            if (Math.abs(data.originalZ) > config.spread) data.velocity.z *= -1;

            positions[i*3] = data.originalX;
            positions[i*3+2] = data.originalZ;
            positions[i*3+1] = Math.sin(data.originalX * 0.015 + time) * 12 + Math.sin(data.originalZ * 0.02 + time * 0.5) * 8;

            for (let j = i + 1; j < config.count; j++) {
                const dx = positions[i*3] - positions[j*3];
                const dy = positions[i*3+1] - positions[j*3+1];
                const dz = positions[i*3+2] - positions[j*3+2];
                const dist = Math.sqrt(dx*dx+dy*dy+dz*dz);
                if (dist < config.linkDist) {
                    linePositions[vertexpos++] = positions[i*3];
                    linePositions[vertexpos++] = positions[i*3+1];
                    linePositions[vertexpos++] = positions[i*3+2];
                    linePositions[vertexpos++] = positions[j*3];
                    linePositions[vertexpos++] = positions[j*3+1];
                    linePositions[vertexpos++] = positions[j*3+2];
                    colors[colorpos++] = lineCol.r; colors[colorpos++] = lineCol.g; colors[colorpos++] = lineCol.b;
                    colors[colorpos++] = lineCol.r; colors[colorpos++] = lineCol.g; colors[colorpos++] = lineCol.b;
                    numConnected++;
                }
            }
        }
        linesMesh.geometry.setDrawRange(0, numConnected * 2);
        linesMesh.geometry.attributes.position.needsUpdate = true;
        linesMesh.geometry.attributes.color.needsUpdate = true;
        pointCloud.geometry.attributes.position.needsUpdate = true;
        renderer.render(scene, camera);
    }

    init();
    animate();

    const resizeObserver = new ResizeObserver(entries => {
        for (let entry of entries) {
            camera.aspect = entry.contentRect.width / entry.contentRect.height;
            camera.updateProjectionMatrix();
            renderer.setSize(entry.contentRect.width, entry.contentRect.height);
        }
    });
    resizeObserver.observe(container);
})();

function copyHTML(btn) {
    const boxContent = btn.parentElement.querySelector('.box-content').innerHTML;
    const cleanHTML = boxContent.trim();
    const tempInput = document.createElement('textarea');
    tempInput.value = cleanHTML;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    const originalText = btn.innerText;
    btn.innerText = "Copied!";
    setTimeout(() => btn.innerText = originalText, 2000);
}
