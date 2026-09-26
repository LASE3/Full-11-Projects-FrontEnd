/**
 * ============================================================================
 * VOSTOKPRIBOR ENTERPRISE DESIGN SYSTEM - SYSTEM 02 (B2B E-COMMERCE)
 * FQDN: shop.vostokpribor.local
 * Consolidated Application Logic (app.js)
 * ============================================================================
 */

(function () {
  "use strict";

  // ==========================================================================
  // DATA DICTIONARY BASELINE (LOCKED ENTERPRISE BASELINE DATA)
  // ==========================================================================

  const CUSTOMERS = [
    {
      id: "CUS-1005",
      name: "Tashkent Precision Controls",
      sector: "Manufacturing",
      contact: "Dilshod Karim",
      accountMgr: "EMP-1008",
      tier: "Enterprise Tier A (12% Disc)",
    },
    {
      id: "CUS-1001",
      name: "Aral Geomatics Group",
      sector: "Geomatics & GIS",
      contact: "Sergei Makarov",
      accountMgr: "EMP-1007",
      tier: "Enterprise Tier A (12% Disc)",
    },
    {
      id: "CUS-1002",
      name: "BaltNord Process Systems",
      sector: "Process Automation",
      contact: "Kristaps Ozols",
      accountMgr: "EMP-1010",
      tier: "Strategic Partner (15% Disc)",
    },
    {
      id: "CUS-1003",
      name: "Steppe Mining Technologies",
      sector: "Mining",
      contact: "Yerlan Bektemis",
      accountMgr: "EMP-1008",
      tier: "Enterprise Tier B (8% Disc)",
    },
    {
      id: "CUS-1004",
      name: "RheinWerk Instrumentation",
      sector: "Industrial Metrology",
      contact: "Lukas Brandt",
      accountMgr: "EMP-1010",
      tier: "Standard Corporate",
    },
    {
      id: "CUS-1007",
      name: "Caspian Industrial Robotics",
      sector: "Robotics",
      contact: "Murad Safarov",
      accountMgr: "EMP-1009",
      tier: "Strategic Partner (15% Disc)",
    },
    {
      id: "CUS-1008",
      name: "Eurasia Water Automation",
      sector: "Water Infrastructure",
      contact: "Oleg Petrenko",
      accountMgr: "EMP-1009",
      tier: "Enterprise Tier B (8% Disc)",
    },
    {
      id: "CUS-1010",
      name: "CentralRail Diagnostics",
      sector: "Railway Infrastructure",
      contact: "Tomas Varga",
      accountMgr: "EMP-1006",
      tier: "Strategic Partner (15% Disc)",
    },
  ];

  const PRODUCTS = [
    {
      id: "PROD-1001",
      sku: "VP-OPT-9020",
      name: "Industrial Optical Sensor Package",
      tagline:
        "Multi-spectral 4K CMOS industrial inspection sensor with sapphire window",
      category: "Optical Sensors",
      categorySlug: "optical-sensors",
      sector: "Geomatics & GIS, Manufacturing, Process Automation",
      billingModel: "Per Unit",
      billingArabic: "",
      price: 5200,
      currency: "$",
      priceFormatted: "$5,200",
      availability: "in-stock",
      stockCount: 38,
      stockText: "38 in Stock (Almaty Central Hub / Bay 14-C)",
      leadTime: "Dispatches in 24 hrs",
      rating: "5.0",
      imageType: "optical_sensor",
      specs: [
        {
          name: "Optical Resolution",
          value: "4K CMOS Matrix (3840 x 2160 px, 60fps)",
        },
        {
          name: "Spectral Waveband",
          value: "400 nm – 1050 nm (VIS-NIR Dual-Spectrum)",
        },
        {
          name: "Field of View (FOV)",
          value: "68° Diagonal, Fixed F/1.4 Sapphire Lens",
        },
        { name: "Precision Tolerance", value: "±0.005 mm at 1.5m focal depth" },
        {
          name: "Interface Protocols",
          value: "Modbus RTU, Profinet, RS-485, GigE Vision",
        },
        {
          name: "Ingress Protection",
          value: "IP68 Hermetic (Submersible 2m / NEMA 6P)",
        },
        { name: "Operating Temp", value: "-40°C to +85°C Industrial Extended" },
        {
          name: "Power Requirements",
          value: "24V DC ±10%, 4.8W max consumption",
        },
        {
          name: "MTBF (Reliability)",
          value: "140,000 Hours (MIL-HDBK-217F Standard)",
        },
        {
          name: "Calibration Cert",
          value: "ISO/IEC 17025:2017 Factory Traceable",
        },
      ],
      description:
        "The VP-OPT-9020 package delivers sub-micron optical metrology and high-speed defect discrimination in demanding industrial environments. Encased in ruggedized anodized aluminum with high-transmittance sapphire optics and automated temperature drift compensation.",
      volumePricing: [
        { qty: "1 - 4 units", price: "$5,200 / ea" },
        { qty: "5 - 19 units", price: "$4,750 / ea" },
        { qty: "20+ units", price: "$4,200 / ea" },
      ],
    },
    {
      id: "PROD-1002",
      sku: "VP-GEO-5500",
      name: "Precision Geodetic Measurement Kit",
      tagline:
        "High-precision dual-frequency GNSS/LiDAR positioning system for harsh topography",
      category: "Measurement Kits",
      categorySlug: "measurement-kits",
      sector: "Geomatics & GIS, Mining, Railway Infrastructure",
      billingModel: "Per Unit",
      billingArabic: "",
      price: 10400,
      currency: "$",
      priceFormatted: "$10,400",
      availability: "in-stock",
      stockCount: 14,
      stockText: "14 in Stock (Almaty Central Hub / Bay 08-A)",
      leadTime: "Dispatches in 24 hrs",
      rating: "4.9",
      imageType: "geodetic_kit",
      specs: [
        {
          name: "LiDAR Measurement Range",
          value: "0.05 m to 250 m (Phase-Shift Coherent)",
        },
        { name: "Angular Precision", value: "0.5 arcsec (0.15 mgon)" },
        {
          name: "GNSS Constellations",
          value: "GPS, GLONASS, Galileo, BeiDou Quad-Band",
        },
        { name: "Ingress Protection", value: "IP67 Dust & Heavy Jet Proof" },
        {
          name: "Interface",
          value: "CANopen, Bluetooth LE 5.2, USB-C Heavy-Duty",
        },
        {
          name: "Battery Operating Time",
          value: "22 hrs continuous (Dual Hot-Swap LiFePO4)",
        },
        { name: "Operating Temp", value: "-30°C to +65°C" },
        { name: "MTBF", value: "110,000 Hours" },
      ],
      description:
        "Engineered for open-pit mining, rail alignment, and GIS survey validation. Combines phase-shift laser distance meters with centimeter-level RTK positioning algorithms and internal inertial measurement units (IMU).",
      volumePricing: [
        { qty: "1 - 2 units", price: "$10,400 / ea" },
        { qty: "3 - 9 units", price: "$9,600 / ea" },
        { qty: "10+ units", price: "$8,800 / ea" },
      ],
    },
    {
      id: "PROD-1003",
      sku: "VP-CAL-8000",
      name: "Automated Calibration Station",
      tagline:
        "Multi-channel environmental sensor verification & recalibration test bench",
      category: "Calibration Stations",
      categorySlug: "calibration-stations",
      sector: "Process Automation, Industrial Metrology, Manufacturing",
      billingModel: "Per Project",
      billingArabic: "",
      price: 17900,
      currency: "$",
      priceFormatted: "$17,900",
      availability: "lead-time",
      stockCount: 4,
      stockText: "Lead Time: 2-3 Weeks (Custom Staging)",
      leadTime: "Built & certified to project requirements",
      rating: "5.0",
      imageType: "calibration_bench",
      specs: [
        {
          name: "Test Channel Capacity",
          value: "8 Independent Sensor Test Bays (Simultaneous)",
        },
        {
          name: "Thermal Testing Chamber",
          value: "-50°C to +150°C Automated PID Profile",
        },
        {
          name: "Pressure Reference",
          value: "0 to 600 Bar (±0.01% FS Accuracy)",
        },
        {
          name: "Interface & Bus",
          value: "SCADA OPC-UA, Modbus TCP, REST API Node",
        },
        {
          name: "Enclosure Rating",
          value: "IP54 Heavy Industrial Control Rack / NEMA 12",
        },
        { name: "Power Input", value: "380V 3-Phase AC, 12kW peak load" },
        { name: "MTBF", value: "85,000 Hours" },
      ],
      description:
        "Fully automated hardware verification system for industrial instrumentation. Performs automated NIST/GOST/ISO traceable calibration curves, pressure leak tests, and thermal drift characterization with automated PDF report generation.",
      volumePricing: [
        { qty: "1 station", price: "$17,900 / project" },
        { qty: "2 - 4 stations", price: "$16,200 / ea" },
        { qty: "5+ stations", price: "Custom Tender Price" },
      ],
    },
    {
      id: "PROD-1004",
      sku: "VP-PLC-X400",
      name: "Industrial PLC Integration Unit",
      tagline:
        "High-availability deterministic controller with dual-redundant fieldbus bridge",
      category: "PLC Integration",
      categorySlug: "plc-integration",
      sector: "Process Automation, Manufacturing, Robotics",
      billingModel: "Per Project",
      billingArabic: "",
      price: 18500,
      currency: "$",
      priceFormatted: "$18,500",
      availability: "custom",
      stockCount: 6,
      stockText: "Engineering Order (14 Days Staging)",
      leadTime: "Pre-configured with custom ladder logic",
      rating: "4.8",
      imageType: "plc_unit",
      specs: [
        {
          name: "Processor",
          value: "Quad-Core ARM Cortex-A72 Real-Time Dual Kernel",
        },
        {
          name: "I/O Channels",
          value: "64 Digital In, 32 Relay Out, 16 Fast Analog (16-bit)",
        },
        {
          name: "Fieldbus Protocols",
          value: "Profinet IRT, EtherCAT, Modbus TCP, MQTT-TLS",
        },
        {
          name: "Redundancy",
          value: "Hot-Standby Dual Controller (<2ms failover)",
        },
        {
          name: "Mounting & Form",
          value: "DIN-Rail TS35 Aluminum Anodized Shield",
        },
        { name: "Operating Temp", value: "-40°C to +75°C Convection Cooled" },
        { name: "MTBF", value: "160,000 Hours" },
      ],
      description:
        "Complete programmable logic controller system engineered for mission-critical manufacturing and automation cells. Compatible with IEC 61131-3 languages (LD, FBD, ST, IL, SFC).",
      volumePricing: [
        { qty: "1 system", price: "$18,500 / project" },
        { qty: "2 - 5 systems", price: "$16,900 / ea" },
        { qty: "6+ systems", price: "$15,400 / ea" },
      ],
    },
    {
      id: "PROD-1005",
      sku: "VP-GTW-320",
      name: "Remote Monitoring Gateway",
      tagline:
        "Cellular/Satellite edge telemetry node with isolated industrial sensor interfaces",
      category: "Monitoring Gateways",
      categorySlug: "monitoring-gateways",
      sector: "Water Infrastructure, Environmental Monitoring, Mining",
      billingModel: "Per Unit",
      billingArabic: "",
      price: 2600,
      currency: "$",
      priceFormatted: "$2,600",
      availability: "in-stock",
      stockCount: 52,
      stockText: "52 in Stock (Almaty Central Hub / Bay 04-C)",
      leadTime: "Dispatches in 24 hrs",
      rating: "4.9",
      imageType: "gateway_box",
      specs: [
        {
          name: "Cellular & Satcom",
          value: "5G / LTE Cat-M1 + Satellite Iridium Fallback",
        },
        {
          name: "Local I/O",
          value: "Dual GigE, RS-485 Galvanic Isolated, 4x 4-20mA",
        },
        {
          name: "Edge Inference",
          value: "TensorFlow Lite Embedded Edge Runtime",
        },
        {
          name: "Power Input",
          value: "9-36V DC Solar/Battery + IEEE 802.3at PoE",
        },
        {
          name: "Ingress Protection",
          value: "IP67 Die-Cast Aluminum Enclosure",
        },
        { name: "Operating Temp", value: "-40°C to +85°C" },
        { name: "MTBF", value: "180,000 Hours" },
      ],
      description:
        "Robust telemetry gateway designed for off-grid remote assets, water pipeline pump stations, and environmental monitoring networks. Transmits secured MQTT/HTTPS payloads with local failover buffering up to 128GB.",
      volumePricing: [
        { qty: "1 - 9 units", price: "$2,600 / ea" },
        { qty: "10 - 49 units", price: "$2,300 / ea" },
        { qty: "50+ units", price: "$2,050 / ea" },
      ],
    },
    {
      id: "PROD-1006",
      sku: "VP-OIS-7200",
      name: "Optical Inspection System",
      tagline:
        "High-speed triple-camera line scan system with AI surface anomaly classifier",
      category: "Optical Sensors",
      categorySlug: "optical-sensors",
      sector: "Optical Engineering, Robotics, Manufacturing",
      billingModel: "Per Project",
      billingArabic: "",
      price: 19800,
      currency: "$",
      priceFormatted: "$19,800",
      availability: "lead-time",
      stockCount: 3,
      stockText: "Lead Time: 3-4 Weeks (Staging Required)",
      leadTime: "Custom optics calibration for target materials",
      rating: "5.0",
      imageType: "optical_system",
      specs: [
        {
          name: "Camera Array",
          value: "Triple Telecentric 25MP Global Shutter CoaXPress",
        },
        {
          name: "Illumination",
          value: "Programmable Dome & Coaxial Structured LED Ring",
        },
        {
          name: "Throughput",
          value: "Up to 1,200 parts / minute at 100% inspection",
        },
        {
          name: "Defect Resolution",
          value: "Down to 0.1 µm scratch / crack discrimination",
        },
        {
          name: "AI Inference Node",
          value: "NVIDIA Jetson AGX Orin Industrial Embedded",
        },
        { name: "MTBF", value: "95,000 Hours" },
      ],
      description:
        "Turnkey inline optical inspection for high-speed manufacturing lines, semiconductor wafer checks, and precision machined components with automated reject gating triggers.",
      volumePricing: [
        { qty: "1 system", price: "$19,800 / project" },
        { qty: "2 - 3 systems", price: "$18,200 / ea" },
        { qty: "4+ systems", price: "Custom Enterprise Agreement" },
      ],
    },
    {
      id: "PROD-1007",
      sku: "VP-SRV-LCS",
      name: "Industrial Lifecycle Support Subscription",
      tagline:
        "24/7/365 telemetry monitoring, critical emergency SLA, and firmware security maintenance",
      category: "Monitoring Gateways",
      categorySlug: "monitoring-gateways",
      sector: "Geomatics & GIS, Railway Infrastructure, Mining",
      billingModel: "Subscription",
      billingArabic: "",
      price: 15400,
      currency: "$",
      priceFormatted: "$15,400 / Yr",
      availability: "in-stock",
      stockCount: 999,
      stockText: "Active Instant Provisioning",
      leadTime: "Activated within 1 hour of PO confirmation",
      rating: "5.0",
      imageType: "support_srv",
      specs: [
        {
          name: "Remote Monitoring",
          value: "24/7/365 Real-Time Telemetry & Alert Escalation",
        },
        {
          name: "Emergency SLA",
          value: "1-Hour Maximum Critical Response (TKT Escalation)",
        },
        {
          name: "Firmware & Patches",
          value: "Quarterly Over-the-Air Hardened Security Updates",
        },
        {
          name: "Advance Spares",
          value: "Next-Business-Day Advance Hardware Replacement",
        },
      ],
      description:
        "Comprehensive enterprise SLA contract ensuring zero unplanned equipment downtime. Includes direct access to Tier-3 engineering staff (EMP-1016, EMP-1018) and emergency replacement stock reservation.",
      volumePricing: [
        { qty: "1 Year Contract", price: "$15,400 / yr" },
        { qty: "3 Year Contract", price: "$13,500 / yr (Save 12%)" },
        { qty: "5 Year Contract", price: "$11,900 / yr (Save 22%)" },
      ],
    },
    {
      id: "PROD-1008",
      sku: "VP-SRV-AUT",
      name: "Automation Software Integration",
      tagline:
        "Custom SCADA/MES middleware connectors and REST/GraphQL ERP integration bridge",
      category: "PLC Integration",
      categorySlug: "plc-integration",
      sector: "Robotics, Manufacturing, Process Automation",
      billingModel: "Per Project",
      billingArabic: "",
      price: 19200,
      currency: "$",
      priceFormatted: "$19,200",
      availability: "custom",
      stockCount: 10,
      stockText: "Immediate Engineering Assignment",
      leadTime: "Sprint delivery in 4-6 weeks",
      rating: "4.9",
      imageType: "software_srv",
      specs: [
        {
          name: "Integration Architecture",
          value: "Enterprise SCADA & MES Middleware Connector",
        },
        {
          name: "ERP Bridges",
          value: "Bi-directional SAP, Oracle NetSuite, 1C Sync",
        },
        {
          name: "Deployment Format",
          value: "Containerized Docker/K8s or Bare-Metal Windows/Linux",
        },
        {
          name: "Governance & Security",
          value: "RBAC, TLS 1.3, Detailed Audit Log Streaming",
        },
      ],
      description:
        "Professional engineering services led by senior integration engineers (EMP-1017, EMP-1020) to bridge physical sensor telemetries and PLC nodes directly with enterprise databases.",
      volumePricing: [
        { qty: "Base Connector", price: "$19,200 / project" },
        { qty: "Multi-Facility Bridge", price: "$19,800 / enterprise" },
      ],
    },
    {
      id: "PROD-1009",
      sku: "VP-SRV-LOG",
      name: "Enterprise Logistics Management",
      tagline:
        "Multi-modal cargo fleet telemetry, customs clearance, and waybill tracking portal",
      category: "Monitoring Gateways",
      categorySlug: "monitoring-gateways",
      sector: "Railway Infrastructure, Mining, Environmental Monitoring",
      billingModel: "Subscription",
      billingArabic: "",
      price: 4100,
      currency: "$",
      priceFormatted: "$4,100 / Mo",
      availability: "in-stock",
      stockCount: 999,
      stockText: "Instant Cloud Tenant Setup",
      leadTime: "Online within 4 hours",
      rating: "4.7",
      imageType: "logistics_srv",
      specs: [
        {
          name: "Asset Fleet Tracking",
          value: "Real-time GPS/GLONASS Telemetry for 250+ Vehicles",
        },
        {
          name: "Cold-Chain Monitoring",
          value: "Temperature, Humidity, & Shock G-Force Logging",
        },
        {
          name: "Waybill Automation",
          value: "Automated Customs & Dispatch Document Generation",
        },
      ],
      description:
        "SaaS logistics visibility platform providing end-to-end tracking for industrial shipments across Central Asia and Eastern Europe.",
      volumePricing: [
        { qty: "Monthly Rolling", price: "$4,100 / mo" },
        { qty: "Annual Commitment", price: "$3,480 / mo (Save 15%)" },
      ],
    },
    {
      id: "PROD-1010",
      sku: "VP-SRV-MNT",
      name: "Preventive Instrument Maintenance",
      tagline:
        "Quarterly on-site calibration, optical seal maintenance, and ISO 17025 certification",
      category: "Calibration Stations",
      categorySlug: "calibration-stations",
      sector: "Geomatics & GIS, Industrial Metrology, Process Automation",
      billingModel: "Annual Contract",
      billingArabic: "",
      price: 10600,
      currency: "$",
      priceFormatted: "$10,600 / Yr",
      availability: "in-stock",
      stockCount: 999,
      stockText: "Annual Service Agreement",
      leadTime: "Quarterly scheduling per client calendar",
      rating: "5.0",
      imageType: "maintenance_srv",
      specs: [
        {
          name: "Field Inspection Visits",
          value: "Quarterly On-Site Metrology Calibration & Clean-Room Test",
        },
        {
          name: "Certification Standard",
          value: "Traceable ISO/IEC 17025 Calibration Reports",
        },
        {
          name: "Consumables Included",
          value: "Optical Sapphire Gaskets, Connectors & O-Rings Replaced",
        },
      ],
      description:
        "Routine preventive servicing and laser interferometer recalibration executed by certified field service technicians to preserve instrument accuracy.",
      volumePricing: [
        { qty: "1 Facility", price: "$10,600 / yr" },
        { qty: "Up to 3 Facilities", price: "$18,900 / yr" },
      ],
    },
  ];

  // ==========================================================================
  // CONFIDENTIAL ORDERS BASELINE (L3 CONFIDENTIAL DATA)
  // ==========================================================================

  const ORDERS = [
    {
      id: "ORD-2026-005",
      project: "PRJ-2026-005",
      customer: "Tashkent Precision Controls",
      customerId: "CUS-1005",
      package: "Industrial PLC Integration + Optical Package",
      value: 26350,
      valueFormatted: "$26,350",
      status: "shipped",
      statusLabel: "Shipped",
      invoice: "INV-2026-005",
      eta: "2026-09-12",
      waybill: "VP-EXP-992014-KZ",
      carrier: "Trans-Eurasia Rail Express",
      timelineStep: 4, // Shipped
      responsible: "EMP-1017 (Dana Yermak, Integration Eng.)",
      dispatchBy: "EMP-1014 (Mikhail Antonov, Warehouse Sup.)",
      items: [
        {
          sku: "PROD-1004",
          name: "Industrial PLC Integration Unit",
          qty: 1,
          price: "$18,500",
        },
        {
          sku: "PROD-1001",
          name: "Industrial Optical Sensor Package",
          qty: 1,
          price: "$5,200",
        },
        {
          sku: "PROD-1007",
          name: "Industrial Lifecycle Support (Setup)",
          qty: 1,
          price: "$2,650",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-09-02 08:30 UTC",
          desc: "Electronic Purchase Order received via B2B Portal. Verified with CRM Lead EMP-1008.",
        },
        {
          step: "Procurement",
          time: "2026-09-04 11:15 UTC",
          desc: "Components requisition approved by Operations (EMP-1013 Ilona Vetra).",
        },
        {
          step: "Fulfillment",
          time: "2026-09-08 16:40 UTC",
          desc: "Hardware staging, calibration bench pass, and ISO 17025 seal applied.",
        },
        {
          step: "Shipped",
          time: "2026-09-10 14:00 UTC",
          desc: "Dispatched via Express Rail Cargo #VP-EXP-992014-KZ. Customs manifest cleared.",
        },
        {
          step: "Delivered",
          time: "Expected 2026-09-12",
          desc: "Delivery in progress to Tashkent Manufacturing Facility #2.",
        },
      ],
    },
    {
      id: "ORD-2026-001",
      project: "PRJ-2026-001",
      customer: "Aral Geomatics Group",
      customerId: "CUS-1001",
      package: "Geodetic Kits & Remote Gateways (Phase 1)",
      value: 16050,
      valueFormatted: "$16,050",
      status: "paid",
      statusLabel: "Fulfillment",
      invoice: "INV-2026-001",
      eta: "2026-09-18",
      waybill: "VP-LOG-448102-KZ",
      carrier: "Central Logistics Direct",
      timelineStep: 3, // Fulfillment
      responsible: "EMP-1019 (Farida Iskakova, Project Mgr.)",
      dispatchBy: "EMP-1014 (Mikhail Antonov, Warehouse Sup.)",
      items: [
        {
          sku: "PROD-1002",
          name: "Precision Geodetic Measurement Kit",
          qty: 4,
          price: "$9,600",
        },
        {
          sku: "PROD-1005",
          name: "Remote Monitoring Gateway",
          qty: 4,
          price: "$4,400",
        },
        {
          sku: "PROD-1010",
          name: "Preventive Instrument Maintenance",
          qty: 1,
          price: "$2,050",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-09-01 09:10 UTC",
          desc: "Statement of Work PRJ-2026-001 validated in File Center.",
        },
        {
          step: "Procurement",
          time: "2026-09-03 14:00 UTC",
          desc: "Geodetic LiDAR components cleared from Central Stock.",
        },
        {
          step: "Fulfillment",
          time: "2026-09-07 10:20 UTC",
          desc: "Pre-shipment optical test passed. Firmware version v4.12.0 flashed.",
        },
        {
          step: "Shipped",
          time: "Pending Dispatch",
          desc: "Scheduled for logistics convoy on 2026-09-14.",
        },
        {
          step: "Delivered",
          time: "Expected 2026-09-18",
          desc: "Aral Geomatics Central Lab, Kyzylorda.",
        },
      ],
    },
    {
      id: "ORD-2026-002",
      project: "PRJ-2026-002",
      customer: "BaltNord Process Systems",
      customerId: "CUS-1002",
      package: "Automated Calibration Station & Automation Bridge",
      value: 19600,
      valueFormatted: "$19,600",
      status: "pending",
      statusLabel: "Procurement",
      invoice: "INV-2026-002",
      eta: "2026-09-28",
      waybill: "Awaiting Carrier Assignment",
      carrier: "Baltic Heavy Freight JSC",
      timelineStep: 2, // Procurement
      responsible: "EMP-1019 (Farida Iskakova, PM)",
      dispatchBy: "EMP-1011 (Arman Tulegenov, Ops Director)",
      items: [
        {
          sku: "PROD-1003",
          name: "Automated Calibration Station",
          qty: 2,
          price: "$11,800",
        },
        {
          sku: "PROD-1008",
          name: "Automation Software Integration",
          qty: 1,
          price: "$5,500",
        },
        {
          sku: "PROD-1007",
          name: "Industrial Lifecycle Support (Year 1)",
          qty: 1,
          price: "$2,300",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-09-05 15:30 UTC",
          desc: "Commercial agreement signed by Kristaps Ozols. Invoice INV-2026-002 issued (Pending).",
        },
        {
          step: "Procurement",
          time: "2026-09-09 11:00 UTC",
          desc: "Specialized thermal chambers ordered from precision engineering partner.",
        },
        {
          step: "Fulfillment",
          time: "Pending Assembly",
          desc: "Staging scheduled at Almaty Workshop Bay 02.",
        },
        {
          step: "Shipped",
          time: "Estimated 2026-09-24",
          desc: "International road transport to Riga, Latvia.",
        },
        {
          step: "Delivered",
          time: "Estimated 2026-09-28",
          desc: "BaltNord Process Systems Industrial Park.",
        },
      ],
    },
    {
      id: "ORD-2026-003",
      project: "PRJ-2026-003",
      customer: "Steppe Mining Technologies",
      customerId: "CUS-1003",
      package: "Geodetic Survey Array & Mining Gateways (Bulk)",
      value: 18750,
      valueFormatted: "$18,750",
      status: "paid",
      statusLabel: "Fulfillment",
      invoice: "INV-2026-003",
      eta: "2026-09-22",
      waybill: "VP-HVY-100922-KZ",
      carrier: "Kazakhstan Mining Logistics",
      timelineStep: 3, // Fulfillment
      responsible: "EMP-1016 (Erik Hansen, Senior Automation Eng.)",
      dispatchBy: "EMP-1012 (Rustam Bekov, Logistics Mgr.)",
      items: [
        {
          sku: "PROD-1002",
          name: "Precision Geodetic Measurement Kit",
          qty: 10,
          price: "$8,800",
        },
        {
          sku: "PROD-1005",
          name: "Remote Monitoring Gateway",
          qty: 15,
          price: "$6,150",
        },
        {
          sku: "PROD-1009",
          name: "Enterprise Logistics Management (Annual)",
          qty: 1,
          price: "$3,800",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-08-25 10:00 UTC",
          desc: "Mining pit survey package contract executed.",
        },
        {
          step: "Procurement",
          time: "2026-08-28 12:00 UTC",
          desc: "Ruggedized IP67 components reserved in batch.",
        },
        {
          step: "Fulfillment",
          time: "2026-09-06 09:15 UTC",
          desc: "Vibration & shock tolerance stress testing completed successfully.",
        },
        {
          step: "Shipped",
          time: "Pending Dispatch",
          desc: "Scheduled for heavy off-road transport to Karaganda Pit #3.",
        },
        {
          step: "Delivered",
          time: "Estimated 2026-09-22",
          desc: "Steppe Mining Central Equipment Depot.",
        },
      ],
    },
    {
      id: "ORD-2026-004",
      project: "PRJ-2026-004",
      customer: "RheinWerk Instrumentation",
      customerId: "CUS-1004",
      package: "Optical Inspection System + Preventive Maintenance",
      value: 19400,
      valueFormatted: "$19,400",
      status: "pending",
      statusLabel: "Shipped",
      invoice: "INV-2026-004",
      eta: "2026-09-14",
      waybill: "VP-AIR-774019-DE",
      carrier: "Lufthansa Cargo Industrial",
      timelineStep: 4, // Shipped
      responsible: "EMP-1019 (Farida Iskakova, PM)",
      dispatchBy: "EMP-1012 (Rustam Bekov, Logistics Mgr.)",
      items: [
        {
          sku: "PROD-1006",
          name: "Optical Inspection System (Base Unit)",
          qty: 1,
          price: "$12,800",
        },
        {
          sku: "PROD-1010",
          name: "Preventive Instrument Maintenance Contract",
          qty: 1,
          price: "$3,400",
        },
        {
          sku: "PROD-1001",
          name: "Spare Optical Sensor Matrix",
          qty: 1,
          price: "$3,200",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-08-30 11:30 UTC",
          desc: "Precision metrology upgrade order logged.",
        },
        {
          step: "Procurement",
          time: "2026-09-02 09:00 UTC",
          desc: "Custom optical prisms sourced and aligned.",
        },
        {
          step: "Fulfillment",
          time: "2026-09-07 15:30 UTC",
          desc: "Clean-room packaging and inert nitrogen sealing.",
        },
        {
          step: "Shipped",
          time: "2026-09-09 18:00 UTC",
          desc: "Air cargo flight departed Almaty International Airport.",
        },
        {
          step: "Delivered",
          time: "Estimated 2026-09-14",
          desc: "RheinWerk Metrology Facility, Munich.",
        },
      ],
    },
    {
      id: "ORD-2026-007",
      project: "PRJ-2026-007",
      customer: "Caspian Industrial Robotics",
      customerId: "CUS-1007",
      package: "Robotic Workcell PLC & Automation Integration",
      value: 19500,
      valueFormatted: "$19,500",
      status: "paid",
      statusLabel: "Delivered",
      invoice: "INV-2026-007",
      eta: "2026-09-01",
      waybill: "VP-EXP-881204-AZ",
      carrier: "Caspian Sea Maritime Express",
      timelineStep: 5, // Delivered
      responsible: "EMP-1019 (Farida Iskakova, PM)",
      dispatchBy: "EMP-1014 (Mikhail Antonov)",
      items: [
        {
          sku: "PROD-1004",
          name: "Industrial PLC Integration Unit (Dual)",
          qty: 2,
          price: "$8,500",
        },
        {
          sku: "PROD-1008",
          name: "Automation Software Integration",
          qty: 1,
          price: "$7,200",
        },
        {
          sku: "PROD-1007",
          name: "Industrial Lifecycle Support (Year 1)",
          qty: 1,
          price: "$3,800",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-08-10 09:00 UTC",
          desc: "Robotics assembly cell tender awarded.",
        },
        {
          step: "Procurement",
          time: "2026-08-14 11:00 UTC",
          desc: "High-speed fieldbus interfaces prepared.",
        },
        {
          step: "Fulfillment",
          time: "2026-08-22 17:00 UTC",
          desc: "Factory Acceptance Test (FAT) passed 100%.",
        },
        {
          step: "Shipped",
          time: "2026-08-27 10:00 UTC",
          desc: "Dispatched across Caspian shipping corridor.",
        },
        {
          step: "Delivered",
          time: "2026-09-01 16:30 UTC",
          desc: "Site Acceptance Test (SAT) signed by Murad Safarov.",
        },
      ],
    },
    {
      id: "ORD-2026-008",
      project: "PRJ-2026-008",
      customer: "Eurasia Water Automation",
      customerId: "CUS-1008",
      package: "Remote Monitoring Gateways (Regional Pumping Network)",
      value: 18733,
      valueFormatted: "$18,733",
      status: "pending",
      statusLabel: "Procurement",
      invoice: "INV-2026-008",
      eta: "2026-09-30",
      waybill: "Awaiting Dispatch Logistics",
      carrier: "National Utility Freight",
      timelineStep: 2, // Procurement
      responsible: "EMP-1017 (Dana Yermak, Integration Eng.)",
      dispatchBy: "EMP-1013 (Ilona Vetra, Procurement Mgr.)",
      items: [
        {
          sku: "PROD-1005",
          name: "Remote Monitoring Gateway",
          qty: 24,
          price: "$9,360",
        },
        {
          sku: "PROD-1007",
          name: "Industrial Lifecycle Support (Network)",
          qty: 1,
          price: "$5,400",
        },
        {
          sku: "PROD-1008",
          name: "SCADA Telemetry Bridge",
          qty: 1,
          price: "$3,973",
        },
      ],
      history: [
        {
          step: "Submitted",
          time: "2026-09-07 14:15 UTC",
          desc: "Pumping network telemetry RFP converted to active project.",
        },
        {
          step: "Procurement",
          time: "2026-09-10 11:30 UTC",
          desc: "Cellular eSIMs and satellite transceivers allocated.",
        },
        {
          step: "Fulfillment",
          time: "Scheduled for 2026-09-16",
          desc: "IP67 enclosure pressure testing in water tank.",
        },
        {
          step: "Shipped",
          time: "Estimated 2026-09-25",
          desc: "Regional delivery across 12 pump substations.",
        },
        {
          step: "Delivered",
          time: "Estimated 2026-09-30",
          desc: "Eurasia Water Automation Central Control, Astana.",
        },
      ],
    },
  ];

  // ==========================================================================
  // STATE MANAGEMENT
  // ==========================================================================

  const state = {
    currentScreen: "catalog", // 'catalog' | 'product-detail' | 'tracking'
    selectedProductId: "PROD-1001",
    activeCustomer: CUSTOMERS[0], // Default: Tashkent Precision Controls
    viewMode: "grid", // 'grid' | 'list'
    searchTerm: "",
    selectedCategories: [],
    selectedSectors: [],
    selectedAvailabilities: [],
    selectedBillingModels: [],
    priceMax: 50000,
    sortBy: "default",
    galleryActiveIndex: 0,
    selectedTrackingOrderId: "ORD-2026-005",
    cartItems: [
      {
        id: "PROD-1001",
        sku: "VP-OPT-9020",
        name: "Industrial Optical Sensor Package",
        qty: 2,
        price: 5200,
        model: "Per Unit",
      },
      {
        id: "PROD-1005",
        sku: "VP-GTW-320",
        name: "Remote Monitoring Gateway",
        qty: 4,
        price: 2600,
        model: "Per Unit",
      },
    ],
    quoteItems: [
      {
        id: "PROD-1004",
        sku: "VP-PLC-X400",
        name: "Industrial PLC Integration Unit",
        qty: 1,
        price: 18500,
        model: "Per Project",
      },
      {
        id: "PROD-1008",
        sku: "VP-SRV-AUT",
        name: "Automation Software Integration",
        qty: 1,
        price: 19200,
        model: "Per Project",
      },
    ],
  };

  // ==========================================================================
  // SVG INDUSTRIAL GRAPHICS GENERATOR (HIGH QUALITY EQUIPMENT RENDERINGS)
  // ==========================================================================

  function getProductSvg(imageType, width = "100%", height = "100%") {
    switch (imageType) {
      case "optical_sensor":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="opt_body" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#1B3A5C"/>
              <stop offset="60%" stop-color="#0F2438"/>
              <stop offset="100%" stop-color="#0A1826"/>
            </linearGradient>
            <linearGradient id="opt_lens" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#4DD8E6"/>
              <stop offset="50%" stop-color="#0E7C86"/>
              <stop offset="100%" stop-color="#084E54"/>
            </linearGradient>
            <linearGradient id="metal_mount" x1="0%" y1="0%" x2="0%" y2="100%">
              <stop offset="0%" stop-color="#8EA1B4"/>
              <stop offset="50%" stop-color="#55697C"/>
              <stop offset="100%" stop-color="#3A4A59"/>
            </linearGradient>
          </defs>
          <!-- Industrial Chassis -->
          <rect x="70" y="35" width="140" height="110" rx="8" fill="url(#opt_body)" stroke="#0E7C86" stroke-width="2"/>
          <!-- Heat Dissipation Fins -->
          <line x1="70" y1="50" x2="60" y2="50" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <line x1="70" y1="65" x2="58" y2="65" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <line x1="70" y1="80" x2="60" y2="80" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <line x1="70" y1="95" x2="58" y2="95" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <line x1="70" y1="110" x2="60" y2="110" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <line x1="70" y1="125" x2="58" y2="125" stroke="#55697C" stroke-width="3" stroke-linecap="round"/>
          <!-- Main Optical Barrel & Lens Ring -->
          <circle cx="140" cy="90" r="42" fill="url(#metal_mount)" stroke="#101418" stroke-width="2"/>
          <circle cx="140" cy="90" r="34" fill="#0A1826" stroke="#4DD8E6" stroke-width="1.5"/>
          <circle cx="140" cy="90" r="26" fill="url(#opt_lens)" stroke="#FFFFFF" stroke-width="0.5" stroke-opacity="0.8"/>
          <!-- Laser Reticle & Crosshairs -->
          <circle cx="140" cy="90" r="14" stroke="#FFFFFF" stroke-width="1" stroke-dasharray="2 2" stroke-opacity="0.7"/>
          <line x1="140" y1="70" x2="140" y2="110" stroke="#E8A33D" stroke-width="1" stroke-opacity="0.8"/>
          <line x1="120" y1="90" x2="160" y2="90" stroke="#E8A33D" stroke-width="1" stroke-opacity="0.8"/>
          <!-- Status LED & Connector Port -->
          <circle cx="195" cy="50" r="4" fill="#2ED573"/>
          <rect x="186" y="118" width="18" height="18" rx="2" fill="#0A1826" stroke="#55697C"/>
          <circle cx="195" cy="127" r="4" fill="#E8A33D"/>
          <!-- Technical Markings -->
          <text x="80" y="52" fill="#4DD8E6" font-family="monospace" font-size="8" font-weight="700">VP-OPT-4K</text>
          <text x="80" y="136" fill="#8EA1B4" font-family="monospace" font-size="7">IP68 / 24VDC</text>
        </svg>`;

      case "geodetic_kit":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="geo_grad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#E8A33D"/>
              <stop offset="60%" stop-color="#1B3A5C"/>
              <stop offset="100%" stop-color="#0F2438"/>
            </linearGradient>
          </defs>
          <!-- Tripod Base -->
          <line x1="90" y1="165" x2="135" y2="115" stroke="#55697C" stroke-width="4" stroke-linecap="round"/>
          <line x1="190" y1="165" x2="145" y2="115" stroke="#55697C" stroke-width="4" stroke-linecap="round"/>
          <line x1="140" y1="165" x2="140" y2="115" stroke="#3A4A59" stroke-width="5" stroke-linecap="round"/>
          <rect x="125" y="108" width="30" height="10" rx="2" fill="#1B3A5C" stroke="#0E7C86"/>
          <!-- Geodetic Total Station Unit -->
          <path d="M120 108 L125 55 L155 55 L160 108 Z" fill="#0F2438" stroke="#1B3A5C" stroke-width="2"/>
          <circle cx="140" cy="78" r="16" fill="#0E7C86" stroke="#FFFFFF" stroke-width="1.5"/>
          <circle cx="140" cy="78" r="8" fill="#4DD8E6"/>
          <!-- GNSS Smart Antenna Dome on Top -->
          <ellipse cx="140" cy="45" rx="26" ry="12" fill="#FFFFFF" stroke="#1B3A5C" stroke-width="2"/>
          <rect x="134" y="32" width="12" height="6" rx="1" fill="#E8A33D"/>
          <line x1="140" y1="32" x2="140" y2="20" stroke="#0E7C86" stroke-width="2"/>
          <!-- Laser Beam Emitter -->
          <line x1="140" y1="78" x2="230" y2="78" stroke="#E8A33D" stroke-width="1.5" stroke-dasharray="3 3"/>
          <text x="80" y="40" fill="#E8A33D" font-family="monospace" font-size="8" font-weight="700">GNSS-RTK</text>
        </svg>`;

      case "calibration_bench":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Multi-bay calibration rack -->
          <rect x="50" y="30" width="180" height="120" rx="6" fill="#0F2438" stroke="#0E7C86" stroke-width="2"/>
          <!-- 4 Calibration Bays -->
          <rect x="62" y="42" width="70" height="46" rx="3" fill="#1B3A5C" stroke="#4DD8E6" stroke-width="1"/>
          <rect x="148" y="42" width="70" height="46" rx="3" fill="#1B3A5C" stroke="#4DD8E6" stroke-width="1"/>
          <rect x="62" y="96" width="70" height="44" rx="3" fill="#1B3A5C" stroke="#55697C" stroke-width="1"/>
          <rect x="148" y="96" width="70" height="44" rx="3" fill="#1B3A5C" stroke="#55697C" stroke-width="1"/>
          <!-- Digital Telemetry Display on Bench -->
          <rect x="70" y="50" width="54" height="28" fill="#0A1826" rx="2"/>
          <text x="74" y="66" fill="#2ED573" font-family="monospace" font-size="9" font-weight="700">23.4°C</text>
          <text x="74" y="75" fill="#4DD8E6" font-family="monospace" font-size="7">CAL: PASS</text>
          <!-- Pressure Gauge Graphic -->
          <circle cx="183" cy="65" r="16" fill="#0A1826" stroke="#8EA1B4"/>
          <line x1="183" y1="65" x2="192" y2="58" stroke="#E8A33D" stroke-width="1.5"/>
          <text x="62" y="156" fill="#8EA1B4" font-family="monospace" font-size="8">ISO/IEC 17025 RACK-08</text>
        </svg>`;

      case "plc_unit":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- DIN-Rail Background Bar -->
          <rect x="30" y="80" width="220" height="20" fill="#8EA1B4" stroke="#55697C"/>
          <!-- PLC Main Unit Module -->
          <rect x="60" y="30" width="75" height="120" rx="4" fill="#0F2438" stroke="#0E7C86" stroke-width="2"/>
          <!-- I/O Expansion Module 1 -->
          <rect x="138" y="35" width="40" height="110" rx="3" fill="#1B3A5C" stroke="#55697C" stroke-width="1.5"/>
          <!-- I/O Expansion Module 2 -->
          <rect x="180" y="35" width="40" height="110" rx="3" fill="#1B3A5C" stroke="#55697C" stroke-width="1.5"/>
          <!-- LED Bank on CPU -->
          <circle cx="75" cy="45" r="3" fill="#2ED573"/>
          <circle cx="85" cy="45" r="3" fill="#2ED573"/>
          <circle cx="95" cy="45" r="3" fill="#E8A33D"/>
          <circle cx="105" cy="45" r="3" fill="#55697C"/>
          <rect x="72" y="60" width="50" height="30" rx="2" fill="#0A1826" stroke="#4DD8E6"/>
          <text x="76" y="76" fill="#4DD8E6" font-family="monospace" font-size="8" font-weight="700">RUN: OK</text>
          <text x="76" y="85" fill="#2ED573" font-family="monospace" font-size="7">PROFINET</text>
          <!-- Terminal Block Pins -->
          <line x1="68" y1="105" x2="126" y2="105" stroke="#E8A33D" stroke-width="2" stroke-dasharray="4 2"/>
          <line x1="68" y1="120" x2="126" y2="120" stroke="#8EA1B4" stroke-width="2" stroke-dasharray="4 2"/>
          <line x1="68" y1="135" x2="126" y2="135" stroke="#E8A33D" stroke-width="2" stroke-dasharray="4 2"/>
        </svg>`;

      case "gateway_box":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Cellular/Satcom Antenna Rods -->
          <line x1="90" y1="20" x2="90" y2="50" stroke="#1B3A5C" stroke-width="4" stroke-linecap="round"/>
          <circle cx="90" cy="18" r="4" fill="#E8A33D"/>
          <line x1="190" y1="20" x2="190" y2="50" stroke="#1B3A5C" stroke-width="4" stroke-linecap="round"/>
          <circle cx="190" cy="18" r="4" fill="#0E7C86"/>
          <!-- Rugged Enclosure Body -->
          <rect x="70" y="50" width="140" height="95" rx="6" fill="#0F2438" stroke="#1B3A5C" stroke-width="2"/>
          <!-- Enclosure Corner Bolt Holes -->
          <circle cx="78" cy="58" r="3" fill="#55697C"/>
          <circle cx="202" cy="58" r="3" fill="#55697C"/>
          <circle cx="78" cy="137" r="3" fill="#55697C"/>
          <circle cx="202" cy="137" r="3" fill="#55697C"/>
          <!-- Front Status Panel -->
          <rect x="85" y="65" width="110" height="40" rx="3" fill="#1B3A5C"/>
          <circle cx="98" cy="85" r="4" fill="#2ED573"/>
          <circle cx="112" cy="85" r="4" fill="#2ED573"/>
          <circle cx="126" cy="85" r="4" fill="#4DD8E6"/>
          <circle cx="140" cy="85" r="4" fill="#E8A33D"/>
          <text x="152" y="88" fill="#FFFFFF" font-family="monospace" font-size="8" font-weight="700">5G / SAT</text>
          <!-- M12 Circular Industrial Connectors on Bottom -->
          <rect x="95" y="145" width="16" height="12" fill="#55697C" rx="1"/>
          <rect x="132" y="145" width="16" height="12" fill="#55697C" rx="1"/>
          <rect x="169" y="145" width="16" height="12" fill="#55697C" rx="1"/>
        </svg>`;

      case "optical_system":
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <!-- Gantry / Frame -->
          <path d="M40 160 L40 30 L240 30 L240 160" stroke="#1B3A5C" stroke-width="6" stroke-linecap="round"/>
          <!-- Conveyor Line Representation -->
          <rect x="30" y="130" width="220" height="14" fill="#55697C"/>
          <!-- 3 Overhead Line-Scan Cameras -->
          <rect x="75" y="40" width="30" height="45" rx="3" fill="#0F2438" stroke="#0E7C86"/>
          <circle cx="90" cy="85" r="8" fill="#4DD8E6"/>
          <rect x="125" y="40" width="30" height="45" rx="3" fill="#0F2438" stroke="#0E7C86"/>
          <circle cx="140" cy="85" r="8" fill="#4DD8E6"/>
          <rect x="175" y="40" width="30" height="45" rx="3" fill="#0F2438" stroke="#0E7C86"/>
          <circle cx="190" cy="85" r="8" fill="#4DD8E6"/>
          <!-- Structured Light Laser Pattern Projected on Part -->
          <polygon points="90,85 70,130 110,130" fill="rgba(14, 124, 134, 0.15)"/>
          <polygon points="140,85 120,130 160,130" fill="rgba(232, 163, 61, 0.2)"/>
          <polygon points="190,85 170,130 210,130" fill="rgba(14, 124, 134, 0.15)"/>
          <!-- Target Machined Part under Inspection -->
          <rect x="120" y="118" width="40" height="12" fill="#E8A33D" rx="1"/>
          <text x="95" y="24" fill="#0E7C86" font-family="monospace" font-size="9" font-weight="700">TRIPLE 25MP ARRAY</text>
        </svg>`;

      default:
        // Service / Support Icon Card Graphic
        return `
        <svg width="${width}" height="${height}" viewBox="0 0 280 180" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="60" y="40" width="160" height="100" rx="8" fill="#0F2438" stroke="#1B3A5C" stroke-width="2"/>
          <circle cx="140" cy="90" r="30" fill="#1B3A5C" stroke="#0E7C86" stroke-width="2"/>
          <path d="M140 70 L140 110 M120 90 L160 90" stroke="#E8A33D" stroke-width="3" stroke-linecap="round"/>
          <text x="95" y="130" fill="#4DD8E6" font-family="monospace" font-size="9">ENTERPRISE SLA</text>
        </svg>`;
    }
  }

  // ==========================================================================
  // VIEW NAVIGATION & ROUTING
  // ==========================================================================

  function switchScreen(screenName, productId = null) {
    state.currentScreen = screenName;
    if (productId) {
      state.selectedProductId = productId;
    }

    // Update screen views visibility
    document
      .querySelectorAll(".screen-view")
      .forEach((el) => el.classList.remove("active"));
    const targetScreen = document.getElementById(`view-${screenName}`);
    if (targetScreen) {
      targetScreen.classList.add("active");
    }

    // Update main nav active indicators
    document.querySelectorAll(".nav-link").forEach((el) => {
      el.classList.remove("active");
      if (el.dataset.screen === screenName) {
        el.classList.add("active");
      }
    });

    // Update breadcrumbs
    updateBreadcrumbs();

    // Render screen specific content
    if (screenName === "catalog") {
      renderCatalog();
    } else if (screenName === "product-detail") {
      renderProductDetail(state.selectedProductId);
    } else if (screenName === "tracking") {
      renderTrackingDashboard();
    }

    window.scrollTo({ top: 0, behavior: "smooth" });
  }

  function updateBreadcrumbs() {
    const breadcrumbsContainer = document.getElementById("app-breadcrumbs");
    if (!breadcrumbsContainer) return;

    if (state.currentScreen === "catalog") {
      breadcrumbsContainer.innerHTML = `
        <span class="breadcrumb-item" onclick="window.shopApp.navigateTo('catalog')">Home</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Industrial B2B Catalog</span>
      `;
    } else if (state.currentScreen === "product-detail") {
      const prod =
        PRODUCTS.find((p) => p.id === state.selectedProductId) || PRODUCTS[0];
      breadcrumbsContainer.innerHTML = `
        <span class="breadcrumb-item" onclick="window.shopApp.navigateTo('catalog')">Catalog</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item" onclick="window.shopApp.filterByCategory('${prod.category}')">${prod.category}</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">${prod.id} (${prod.name})</span>
      `;
    } else if (state.currentScreen === "tracking") {
      breadcrumbsContainer.innerHTML = `
        <span class="breadcrumb-item" onclick="window.shopApp.navigateTo('catalog')">Operations</span>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-item active">Order Tracking & Fulfillment Timeline</span>
      `;
    }
  }

  // ==========================================================================
  // SCREEN 1: PRODUCT CATALOG RENDERING & FILTERING
  // ==========================================================================

  function getFilteredProducts() {
    return PRODUCTS.filter((product) => {
      // Search text match
      if (state.searchTerm) {
        const query = state.searchTerm.toLowerCase();
        const matchTitle = product.name.toLowerCase().includes(query);
        const matchSku = product.sku.toLowerCase().includes(query);
        const matchId = product.id.toLowerCase().includes(query);
        const matchDesc = product.description.toLowerCase().includes(query);
        const matchCat = product.category.toLowerCase().includes(query);
        if (!matchTitle && !matchSku && !matchId && !matchDesc && !matchCat)
          return false;
      }

      // Category filter
      if (state.selectedCategories.length > 0) {
        if (!state.selectedCategories.includes(product.category)) return false;
      }

      // Sector filter
      if (state.selectedSectors.length > 0) {
        const productSectors = product.sector.split(",").map((s) => s.trim());
        const hasMatchingSector = state.selectedSectors.some((sec) =>
          productSectors.includes(sec),
        );
        if (!hasMatchingSector) return false;
      }

      // Availability filter
      if (state.selectedAvailabilities.length > 0) {
        if (!state.selectedAvailabilities.includes(product.availability))
          return false;
      }

      // Billing Model filter
      if (state.selectedBillingModels.length > 0) {
        if (!state.selectedBillingModels.includes(product.billingModel))
          return false;
      }

      // Max price filter
      if (product.price > state.priceMax) return false;

      return true;
    }).sort((a, b) => {
      if (state.sortBy === "price-asc") return a.price - b.price;
      if (state.sortBy === "price-desc") return b.price - a.price;
      if (state.sortBy === "sku") return a.id.localeCompare(b.id);
      if (state.sortBy === "availability")
        return a.availability.localeCompare(b.availability);
      return 0; // default
    });
  }

  function renderCatalog() {
    const gridContainer = document.getElementById("catalog-products-grid");
    const resultsCountEl = document.getElementById("catalog-results-count");
    const activeFiltersEl = document.getElementById("catalog-active-filters");

    if (!gridContainer) return;

    const filtered = getFilteredProducts();

    if (resultsCountEl) {
      resultsCountEl.innerHTML = `Showing <strong>${filtered.length}</strong> industrial equipment packages`;
    }

    // Render Active Filters Chips
    if (activeFiltersEl) {
      let chipsHtml = "";
      if (state.searchTerm) {
        chipsHtml += `<div class="filter-chip">Query: <strong>"${state.searchTerm}"</strong> <span class="chip-remove" onclick="window.shopApp.clearSearch()">×</span></div>`;
      }
      state.selectedCategories.forEach((cat) => {
        chipsHtml += `<div class="filter-chip">Category: <strong>${cat}</strong> <span class="chip-remove" onclick="window.shopApp.toggleCategoryFilter('${cat}')">×</span></div>`;
      });
      state.selectedSectors.forEach((sec) => {
        chipsHtml += `<div class="filter-chip">Sector: <strong>${sec}</strong> <span class="chip-remove" onclick="window.shopApp.toggleSectorFilter('${sec}')">×</span></div>`;
      });
      state.selectedAvailabilities.forEach((av) => {
        chipsHtml += `<div class="filter-chip">Availability: <strong>${av}</strong> <span class="chip-remove" onclick="window.shopApp.toggleAvailabilityFilter('${av}')">×</span></div>`;
      });
      state.selectedBillingModels.forEach((bm) => {
        chipsHtml += `<div class="filter-chip">Model: <strong>${bm}</strong> <span class="chip-remove" onclick="window.shopApp.toggleBillingModelFilter('${bm}')">×</span></div>`;
      });

      if (state.priceMax < 50000) {
        chipsHtml += `<div class="filter-chip">Max: <strong>$${state.priceMax.toLocaleString()}</strong> <span class="chip-remove" onclick="window.shopApp.resetPriceFilter()">×</span></div>`;
      }

      activeFiltersEl.innerHTML = chipsHtml;
    }

    if (filtered.length === 0) {
      gridContainer.innerHTML = `
        <div style="grid-column: 1 / -1; padding: 48px; text-align: center; background: #FFFFFF; border: 1px dashed var(--border-subtle); border-radius: var(--radius-md);">
          <div style="font-size: 32px; color: var(--text-tertiary); margin-bottom: 12px;">⚙️</div>
          <h3 style="font-size: 16px; font-weight: 700; color: var(--secondary-brand); margin-bottom: 6px;">No Industrial Equipment Matches Specified Filter Parameters</h3>
          <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">Try adjusting your sector requirements, price ceiling, or category selections.</p>
          <button class="btn-primary-amber" onclick="window.shopApp.resetAllFilters()">Reset All Filters</button>
        </div>
      `;
      return;
    }

    gridContainer.className = `product-grid ${state.viewMode === "list" ? "list-view" : ""}`;

    gridContainer.innerHTML = filtered
      .map((product) => {
        let availClass = "in-stock";
        let availText = `${product.stockCount} in Stock`;
        if (product.availability === "lead-time") {
          availClass = "lead-time";
          availText = "Lead Time: 2-3 Wks";
        } else if (product.availability === "custom") {
          availClass = "custom";
          availText = "Engineering Order";
        }

        const keySpecs = product.specs
          .slice(0, 3)
          .map(
            (s) =>
              `<span class="spec-pill">${s.name}: ${s.value.split("(")[0].trim()}</span>`,
          )
          .join("");

        return `
        <div class="product-card" data-product-id="${product.id}">
          <div class="product-card__visual" onclick="window.shopApp.openProductDetail('${product.id}')">
            ${getProductSvg(product.imageType, "100%", "150px")}
            <span class="badge-sku">${product.id} | ${product.sku}</span>
            <span class="badge-availability ${availClass}">
              <span class="availability-dot"></span>
              ${availText}
            </span>
          </div>

          <div class="product-card__body">
            <div class="product-card__category">${product.category}</div>
            <h3 class="product-card__title" onclick="window.shopApp.openProductDetail('${product.id}')">${product.name}</h3>
            <p class="product-card__desc">${product.tagline}</p>
            
            <div class="product-card__specs-row">
              ${keySpecs}
            </div>

            <div class="product-card__footer">
              <div class="product-pricing-block">
                <span class="billing-model-tag">${product.billingModel}</span>
                <div class="price-value">${product.priceFormatted}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button class="btn-secondary-outline" style="padding: 7px 10px; font-size: 11.5px;" onclick="window.shopApp.openProductDetail('${product.id}')">
                  Specs
                </button>
                <button class="btn-primary-amber" onclick="window.shopApp.addToQuote('${product.id}', event)">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                  Add to Quote
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      })
      .join("");
  }

  // ==========================================================================
  // SCREEN 2: PRODUCT DETAIL PAGE (PDP) RENDERING
  // ==========================================================================

  function renderProductDetail(productId) {
    const product = PRODUCTS.find((p) => p.id === productId) || PRODUCTS[0];
    state.selectedProductId = product.id;

    const pdpContainer = document.getElementById("pdp-dynamic-content");
    if (!pdpContainer) return;

    // Gallery angles
    const galleryViews = [
      { label: "Front Ortho", desc: "Main Package Assembly" },
      { label: "Optical Core", desc: "4K Matrix & Sapphire Lens" },
      { label: "Terminal Pinout", desc: "RS-485 / Modbus Wiring" },
      { label: "CAD Blueprints", desc: "Dimensional Tolerance Spec" },
    ];

    let availDotClass = product.availability === "in-stock" ? "green" : "amber";

    const specRows = product.specs
      .map(
        (spec) => `
      <tr>
        <td class="spec-name">${spec.name}</td>
        <td class="spec-value">${spec.value}</td>
      </tr>
    `,
      )
      .join("");

    const volumeTiersHtml = product.volumePricing
      ? product.volumePricing
          .map(
            (tier, idx) => `
      <div class="tier-card ${idx === 0 ? "active" : ""}">
        <span class="tier-range">${tier.qty}</span>
        <span class="tier-price">${tier.price}</span>
      </div>
    `,
          )
          .join("")
      : "";

    // Related Services from baseline: PROD-1008, PROD-1007, PROD-1010, PROD-1003
    const relatedServices = PRODUCTS.filter((p) =>
      ["PROD-1008", "PROD-1007", "PROD-1010"].includes(p.id),
    );

    const relatedCardsHtml = relatedServices
      .map(
        (srv) => `
      <div class="service-card">
        <div class="service-card__header">
          <div class="service-icon-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <div style="flex: 1;">
            <div class="service-sku">${srv.id} | ${srv.billingModel}</div>
            <div class="service-name">${srv.name}</div>
          </div>
        </div>
        <p class="service-desc">${srv.tagline}</p>
        <div class="service-features-list">
          <div class="service-feature-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Linked to CRM Opportunity & Project Statement of Work</span>
          </div>
          <div class="service-feature-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Technical integration support by Senior Lead EMP-1016</span>
          </div>
        </div>
        <div class="service-card__footer">
          <div class="price-value" style="font-size: 15px;">${srv.priceFormatted}</div>
          <button class="btn-primary-amber" style="padding: 6px 12px; font-size: 11.5px;" onclick="window.shopApp.addToQuote('${srv.id}')">
            Add Service to Quote
          </button>
        </div>
      </div>
    `,
      )
      .join("");

    pdpContainer.innerHTML = `
      <div class="pdp-container">
        <!-- Back Bar -->
        <div class="pdp-nav-back-bar">
          <button class="btn-back-catalog" onclick="window.shopApp.navigateTo('catalog')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Industrial Catalog
          </button>
          <div class="pdp-system-compliance-pill">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>VOSTOKPRIBOR ENTERPRISE BASELINE VERIFIED · FQDN: shop.vostokpribor.local</span>
          </div>
        </div>

        <!-- Main Product Split Layout -->
        <div class="pdp-main-grid">
          <!-- Left Gallery Column -->
          <div class="gallery-column">
            <div class="gallery-viewport" id="pdp-main-viewport">
              ${getProductSvg(product.imageType, "80%", "300px")}
              <div class="gallery-viewport-overlay">
                <span class="viewport-badge">RENDER: CAD VIEW #0${state.galleryActiveIndex + 1}</span>
                <span class="gallery-zoom-hint">🔍 Optical Trace 1:1</span>
              </div>
            </div>

            <!-- Multi-angle Thumbnails -->
            <div class="gallery-thumbnails-row">
              ${galleryViews
                .map(
                  (view, idx) => `
                <div class="gallery-thumb ${idx === state.galleryActiveIndex ? "active" : ""}" onclick="window.shopApp.setGalleryIndex(${idx})">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="4"/></svg>
                  <span>${view.label}</span>
                </div>
              `,
                )
                .join("")}
            </div>

            <!-- CAD & Technical Downloads Box -->
            <div class="pdp-downloads-card">
              <div class="downloads-title">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                <span>Engineering Documentation & CAD Packages</span>
              </div>
              <div class="downloads-buttons-grid">
                <button class="btn-download-asset" onclick="window.shopApp.downloadDoc('DOC-2026-009', 'Datasheet')">
                  📄 PDF Specs
                </button>
                <button class="btn-download-asset" onclick="window.shopApp.downloadDoc('STEP-3D-MODEL', 'STEP File')">
                  📐 3D STEP
                </button>
                <button class="btn-download-asset" onclick="window.shopApp.downloadDoc('ISO-17025-CERT', 'Certificate')">
                  🛡️ ISO 17025
                </button>
              </div>
            </div>
          </div>

          <!-- Right Product Information & Buy Box Column -->
          <div class="pdp-info-column">
            <div class="pdp-header-block">
              <div class="pdp-meta-tags-row">
                <span class="sku-tag-prominent">${product.id} · ${product.sku}</span>
                <span class="billing-badge-prominent">${product.billingModel}</span>
                <span class="classification-badge">SECURITY: L1 PUBLIC / L3 RESTRICTED</span>
              </div>
              <h1 class="pdp-product-title">${product.name}</h1>
              <p class="pdp-product-tagline">${product.description}</p>
            </div>

            <!-- Real-time Stock Bar -->
            <div class="stock-indicator-bar">
              <div class="stock-status-left">
                <span class="stock-status-dot ${availDotClass}"></span>
                <strong>${product.stockText}</strong>
              </div>
              <div class="stock-location-right">
                Lead: ${product.leadTime}
              </div>
            </div>

            <!-- Volume Pricing Matrix -->
            <div class="pricing-matrix-box">
              <div class="price-main-display">
                <div>
                  <span style="font-size: 11px; color: var(--text-tertiary); display: block;">STANDARD B2B UNIT PRICE</span>
                  <div class="price-unit-large">${product.priceFormatted}</div>
                </div>
                <div style="text-align: right;">
                  <span class="price-client-tier">Client: ${state.activeCustomer.name}</span>
                  <div style="font-size: 11px; color: var(--text-secondary);">${state.activeCustomer.tier}</div>
                </div>
              </div>

              ${volumeTiersHtml ? `<div class="volume-tiers-row">${volumeTiersHtml}</div>` : ""}
            </div>

            <!-- Technical Specification Table -->
            <div class="spec-section">
              <div class="spec-table-heading">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <span>Certified Engineering Parameters</span>
              </div>
              <table class="spec-table">
                <tbody>
                  ${specRows}
                </tbody>
              </table>
            </div>

            <!-- Quantity Selector & Prominent CTAs -->
            <div class="pdp-actions-row">
              <div class="quantity-control">
                <button class="qty-btn" onclick="window.shopApp.decrementPdpQty()">−</button>
                <input type="text" id="pdp-qty-input" class="qty-input" value="1" readonly>
                <button class="qty-btn" onclick="window.shopApp.incrementPdpQty()">+</button>
              </div>

              <button class="btn-primary-amber pdp-btn-quote" onclick="window.shopApp.submitPdpQuote('${product.id}')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Request Official Quote
              </button>

              <button class="btn-secondary-outline pdp-btn-cart" onclick="window.shopApp.submitPdpCart('${product.id}')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Add to Cart
              </button>
            </div>
          </div>
        </div>

        <!-- Related Services Row -->
        <div class="related-services-section">
          <div class="section-header-row">
            <h2 class="section-title">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              <span>Recommended Integration & Lifecycle Services</span>
            </h2>
            <span class="section-subtext">Unified with Operations (OPS) and Engineering (ENG) teams</span>
          </div>

          <div class="services-grid">
            ${relatedCardsHtml}
          </div>
        </div>
      </div>
    `;
  }

  // ==========================================================================
  // SCREEN 3: ORDER TRACKING DASHBOARD & TIMELINE
  // ==========================================================================

  function renderTrackingDashboard() {
    const selectedOrder =
      ORDERS.find((o) => o.id === state.selectedTrackingOrderId) || ORDERS[0];
    const timelineContainer = document.getElementById(
      "tracking-timeline-container",
    );
    const tableContainer = document.getElementById(
      "tracking-orders-table-body",
    );
    const orderSelectEl = document.getElementById("tracking-order-select");

    if (orderSelectEl) {
      orderSelectEl.innerHTML = ORDERS.map(
        (o) => `
        <option value="${o.id}" ${o.id === selectedOrder.id ? "selected" : ""}>
          ${o.id} - ${o.customer} (${o.valueFormatted})
        </option>
      `,
      ).join("");
    }

    // Render Timeline Component at Top
    if (timelineContainer) {
      const steps = [
        { id: 1, name: "Submitted", key: "Submitted" },
        { id: 2, name: "Procurement", key: "Procurement" },
        { id: 3, name: "Fulfillment", key: "Fulfillment" },
        { id: 4, name: "Shipped", key: "Shipped" },
        { id: 5, name: "Delivered", key: "Delivered" },
      ];

      const currentStepNum = selectedOrder.timelineStep;
      const progressPercent = ((currentStepNum - 1) / (steps.length - 1)) * 100;

      const stepsHtml = steps
        .map((step, idx) => {
          let stepClass = "";
          if (step.id < currentStepNum) {
            stepClass = "completed";
          } else if (step.id === currentStepNum) {
            stepClass = "active";
          }

          const histItem = selectedOrder.history.find(
            (h) => h.step === step.key,
          ) || { time: "Pending", desc: "" };

          return `
          <div class="timeline-step ${stepClass}">
            <div class="step-node">
              ${step.id < currentStepNum ? "✓" : `0${step.id}`}
            </div>
            <div class="step-name">${step.name}</div>
            <div class="step-timestamp">${histItem.time.split("UTC")[0].trim()}</div>
            <div class="step-meta">${step.id === currentStepNum ? "Current Stage" : step.id < currentStepNum ? "Completed" : "Upcoming"}</div>
          </div>
        `;
        })
        .join("");

      timelineContainer.innerHTML = `
        <div class="timeline-card">
          <div class="timeline-header">
            <div class="timeline-order-info">
              <span class="timeline-order-id">${selectedOrder.id}</span>
              <span class="timeline-project-badge">${selectedOrder.project}</span>
              <span class="timeline-client-tag">Account: <strong>${selectedOrder.customer}</strong> (${selectedOrder.customerId})</span>
            </div>

            <div class="timeline-controls">
              <label style="font-size: 11px; color: var(--text-secondary); font-weight: 600;">ACTIVE ORDER:</label>
              <select class="order-select-dropdown" onchange="window.shopApp.selectTrackingOrder(this.value)">
                ${ORDERS.map((o) => `<option value="${o.id}" ${o.id === selectedOrder.id ? "selected" : ""}>${o.id} (${o.statusLabel}) - ${o.valueFormatted}</option>`).join("")}
              </select>
            </div>
          </div>

          <!-- Status Timeline Bar -->
          <div class="horizontal-timeline">
            <div class="timeline-connector">
              <div class="timeline-connector-progress" style="width: ${progressPercent}%;"></div>
            </div>
            ${stepsHtml}
          </div>

          <!-- Timeline Live Activity Footer -->
          <div class="timeline-live-footer">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="live-badge-teal">STAGE: ${selectedOrder.statusLabel.toUpperCase()}</span>
              <span><strong>Waybill:</strong> ${selectedOrder.waybill} · Carrier: ${selectedOrder.carrier}</span>
            </div>
            <div>
              <span><strong>Assigned Engineer:</strong> ${selectedOrder.responsible} | <strong>Warehouse:</strong> ${selectedOrder.dispatchBy}</span>
            </div>
          </div>
        </div>
      `;
    }

    // Render Confidential Orders Table with Amber-Orange Left-Border Tag (#D9822B)
    if (tableContainer) {
      tableContainer.innerHTML = ORDERS.map((order) => {
        let statusPillClass = "pending";
        if (order.status === "paid") statusPillClass = "paid";
        if (order.status === "shipped") statusPillClass = "shipped";
        if (order.status === "processing") statusPillClass = "processing";

        return `
          <tr class="confidential-row" onclick="window.shopApp.inspectOrderDetails('${order.id}')">
            <td class="mono">
              <span style="color: var(--secondary-brand); font-weight: 700;">${order.id}</span>
              <div style="font-size: 10px; color: var(--text-tertiary);">${order.invoice}</div>
            </td>
            <td class="mono">
              <span style="background: var(--bg-surface-alt); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border-light);">${order.project}</span>
            </td>
            <td>
              <strong style="color: var(--text-primary);">${order.customer}</strong>
              <div style="font-size: 10.5px; color: var(--text-secondary); font-family: var(--font-mono);">${order.customerId}</div>
            </td>
            <td>
              <div style="font-size: 12px; color: var(--text-primary); max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${order.package}
              </div>
            </td>
            <td class="mono" style="font-size: 13.5px; font-weight: 700;">
              ${order.valueFormatted}
            </td>
            <td>
              <span class="status-pill ${statusPillClass}">
                <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                ${order.statusLabel}
              </span>
            </td>
            <td class="mono" style="color: var(--text-secondary);">
              ${order.eta}
            </td>
            <td style="text-align: right;">
              <button class="btn-table-action" onclick="event.stopPropagation(); window.shopApp.selectTrackingOrder('${order.id}')">
                Inspect Timeline
              </button>
            </td>
          </tr>
        `;
      }).join("");
    }
  }

  // ==========================================================================
  // CART & RFQ STATE MANAGEMENT
  // ==========================================================================

  function addToQuote(productId, event) {
    if (event) event.stopPropagation();
    const product = PRODUCTS.find((p) => p.id === productId);
    if (!product) return;

    const existing = state.quoteItems.find((item) => item.id === productId);
    if (existing) {
      existing.qty += 1;
    } else {
      state.quoteItems.push({
        id: product.id,
        sku: product.sku,
        name: product.name,
        qty: 1,
        price: product.price,
        model: product.billingModel,
      });
    }

    updateBadgeCounts();
    showToast(`Added "${product.name}" to Quotation Package (RFQ)`, "teal");
    openRfqDrawer();
  }

  function addToCart(productId, qty = 1) {
    const product = PRODUCTS.find((p) => p.id === productId);
    if (!product) return;

    const existing = state.cartItems.find((item) => item.id === productId);
    if (existing) {
      existing.qty += qty;
    } else {
      state.cartItems.push({
        id: product.id,
        sku: product.sku,
        name: product.name,
        qty: qty,
        price: product.price,
        model: product.billingModel,
      });
    }

    updateBadgeCounts();
    showToast(`Added ${qty}x "${product.name}" to Direct B2B Cart`, "green");
    openCartDrawer();
  }

  function updateBadgeCounts() {
    const cartBadge = document.getElementById("header-cart-badge");
    const quoteNavBadge = document.getElementById("nav-quotes-count");

    const totalCartQty = state.cartItems.reduce((sum, i) => sum + i.qty, 0);
    const totalQuoteQty = state.quoteItems.reduce((sum, i) => sum + i.qty, 0);

    if (cartBadge) cartBadge.innerText = totalCartQty;
    if (quoteNavBadge) quoteNavBadge.innerText = totalQuoteQty;
  }

  // ==========================================================================
  // DRAWERS & MODALS
  // ==========================================================================

  function openRfqDrawer() {
    const drawer = document.getElementById("rfq-drawer");
    const overlay = document.getElementById("modal-overlay");
    const listEl = document.getElementById("rfq-items-list");
    const totalEl = document.getElementById("rfq-estimated-total");

    if (!drawer || !overlay) return;

    if (listEl) {
      if (state.quoteItems.length === 0) {
        listEl.innerHTML = `<div style="text-align: center; color: var(--text-tertiary); padding: 24px;">No equipment packages currently staged in RFQ.</div>`;
      } else {
        listEl.innerHTML = state.quoteItems
          .map(
            (item) => `
          <div class="drawer-item-card">
            <div class="drawer-item-info">
              <span class="drawer-item-sku">${item.id} · ${item.sku}</span>
              <span class="drawer-item-name">${item.name} (${item.qty}x)</span>
              <span style="font-size: 11px; color: var(--text-secondary);">${item.model}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="drawer-item-price">$${(item.price * item.qty).toLocaleString()}</div>
              <span class="drawer-item-remove" onclick="window.shopApp.removeQuoteItem('${item.id}')">✕</span>
            </div>
          </div>
        `,
          )
          .join("");
      }
    }

    const total = state.quoteItems.reduce((sum, i) => sum + i.price * i.qty, 0);
    if (totalEl) totalEl.innerText = `$${total.toLocaleString()}`;

    overlay.classList.add("open");
    drawer.classList.add("open");
  }

  function openCartDrawer() {
    const drawer = document.getElementById("cart-drawer");
    const overlay = document.getElementById("modal-overlay");
    const listEl = document.getElementById("cart-items-list");
    const subtotalEl = document.getElementById("cart-subtotal");
    const totalEl = document.getElementById("cart-total");

    if (!drawer || !overlay) return;

    if (listEl) {
      if (state.cartItems.length === 0) {
        listEl.innerHTML = `<div style="text-align: center; color: var(--text-tertiary); padding: 24px;">Your direct procurement cart is empty.</div>`;
      } else {
        listEl.innerHTML = state.cartItems
          .map(
            (item) => `
          <div class="drawer-item-card">
            <div class="drawer-item-info">
              <span class="drawer-item-sku">${item.id} · ${item.sku}</span>
              <span class="drawer-item-name">${item.name}</span>
              <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                <span style="font-size: 11px; color: var(--text-secondary);">Qty: <strong>${item.qty}</strong></span>
                <span style="font-size: 10px; color: var(--text-tertiary);">(${item.model})</span>
              </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="drawer-item-price">$${(item.price * item.qty).toLocaleString()}</div>
              <span class="drawer-item-remove" onclick="window.shopApp.removeCartItem('${item.id}')">✕</span>
            </div>
          </div>
        `,
          )
          .join("");
      }
    }

    const subtotal = state.cartItems.reduce(
      (sum, i) => sum + i.price * i.qty,
      0,
    );
    if (subtotalEl) subtotalEl.innerText = `$${subtotal.toLocaleString()}`;
    if (totalEl) totalEl.innerText = `$${subtotal.toLocaleString()}`;

    overlay.classList.add("open");
    drawer.classList.add("open");
  }

  function closeAllModals() {
    document
      .querySelectorAll(".drawer-container")
      .forEach((d) => d.classList.remove("open"));
    const overlay = document.getElementById("modal-overlay");
    if (overlay) overlay.classList.remove("open");
  }

  function showToast(message, type = "amber") {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <span style="font-size: 16px;">${type === "green" ? "✓" : type === "teal" ? "⚙️" : "🔔"}</span>
      <div>${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateX(100%)";
      toast.style.transition = "all 0.3s ease";
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }

  // ==========================================================================
  // GLOBAL PUBLIC API EXPOSURE
  // ==========================================================================

  window.shopApp = {
    init: function () {
      renderCatalog();
      updateBadgeCounts();
      updateBreadcrumbs();
      this.initEventListeners();
    },

    navigateTo: function (screenName) {
      switchScreen(screenName);
    },

    openProductDetail: function (productId) {
      switchScreen("product-detail", productId);
    },

    setGalleryIndex: function (index) {
      state.galleryActiveIndex = index;
      const viewport = document.getElementById("pdp-main-viewport");
      const prod =
        PRODUCTS.find((p) => p.id === state.selectedProductId) || PRODUCTS[0];
      if (viewport) {
        viewport.innerHTML = `
          ${getProductSvg(prod.imageType, "80%", "300px")}
          <div class="gallery-viewport-overlay">
            <span class="viewport-badge">RENDER: CAD VIEW #0${index + 1}</span>
            <span class="gallery-zoom-hint">🔍 Optical Trace 1:1</span>
          </div>
        `;
      }
      document.querySelectorAll(".gallery-thumb").forEach((thumb, i) => {
        thumb.classList.toggle("active", i === index);
      });
    },

    incrementPdpQty: function () {
      const input = document.getElementById("pdp-qty-input");
      if (input) {
        let val = parseInt(input.value) || 1;
        input.value = val + 1;
      }
    },

    decrementPdpQty: function () {
      const input = document.getElementById("pdp-qty-input");
      if (input) {
        let val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
      }
    },

    submitPdpQuote: function (productId) {
      const input = document.getElementById("pdp-qty-input");
      const qty = input ? parseInt(input.value) || 1 : 1;
      const product = PRODUCTS.find((p) => p.id === productId);
      if (!product) return;

      const existing = state.quoteItems.find((item) => item.id === productId);
      if (existing) {
        existing.qty += qty;
      } else {
        state.quoteItems.push({
          id: product.id,
          sku: product.sku,
          name: product.name,
          qty: qty,
          price: product.price,
          model: product.billingModel,
        });
      }

      updateBadgeCounts();
      showToast(
        `Added ${qty}x "${product.name}" to Official Quote RFQ`,
        "teal",
      );
      openRfqDrawer();
    },

    submitPdpCart: function (productId) {
      const input = document.getElementById("pdp-qty-input");
      const qty = input ? parseInt(input.value) || 1 : 1;
      addToCart(productId, qty);
    },

    addToQuote: function (productId, event) {
      addToQuote(productId, event);
    },

    removeQuoteItem: function (productId) {
      state.quoteItems = state.quoteItems.filter((i) => i.id !== productId);
      updateBadgeCounts();
      openRfqDrawer();
      showToast("Item removed from Quote", "teal");
    },

    removeCartItem: function (productId) {
      state.cartItems = state.cartItems.filter((i) => i.id !== productId);
      updateBadgeCounts();
      openCartDrawer();
      showToast("Item removed from Direct Cart", "amber");
    },

    openRfqDrawer: openRfqDrawer,
    openCartDrawer: openCartDrawer,
    closeAllModals: closeAllModals,

    selectTrackingOrder: function (orderId) {
      state.selectedTrackingOrderId = orderId;
      if (state.currentScreen !== "tracking") {
        switchScreen("tracking");
      } else {
        renderTrackingDashboard();
      }
      showToast(
        `Loaded Live Telemetry & Timeline for Order ${orderId}`,
        "teal",
      );
    },

    inspectOrderDetails: function (orderId) {
      this.selectTrackingOrder(orderId);
      const targetCard = document.querySelector(".timeline-card");
      if (targetCard) {
        targetCard.scrollIntoView({ behavior: "smooth" });
      }
    },

    downloadDoc: function (docId, type) {
      showToast(
        `Generating certified PDF Package for ${docId} (${type})...`,
        "teal",
      );
      setTimeout(() => {
        showToast(
          `Document ${docId} retrieved from File Center (System 09)`,
          "green",
        );
      }, 1200);
    },

    // Filters and Search
    setSearchTerm: function (term) {
      state.searchTerm = term;
      renderCatalog();
    },

    clearSearch: function () {
      state.searchTerm = "";
      const input = document.getElementById("catalog-search-input");
      if (input) input.value = "";
      renderCatalog();
    },

    toggleCategoryFilter: function (category) {
      const idx = state.selectedCategories.indexOf(category);
      if (idx > -1) {
        state.selectedCategories.splice(idx, 1);
      } else {
        state.selectedCategories.push(category);
      }
      this.syncFilterCheckboxes();
      renderCatalog();
    },

    filterByCategory: function (category) {
      state.selectedCategories = [category];
      this.syncFilterCheckboxes();
      switchScreen("catalog");
    },

    toggleSectorFilter: function (sector) {
      const idx = state.selectedSectors.indexOf(sector);
      if (idx > -1) {
        state.selectedSectors.splice(idx, 1);
      } else {
        state.selectedSectors.push(sector);
      }
      this.syncFilterCheckboxes();
      renderCatalog();
    },

    toggleAvailabilityFilter: function (av) {
      const idx = state.selectedAvailabilities.indexOf(av);
      if (idx > -1) {
        state.selectedAvailabilities.splice(idx, 1);
      } else {
        state.selectedAvailabilities.push(av);
      }
      this.syncFilterCheckboxes();
      renderCatalog();
    },

    toggleBillingModelFilter: function (bm) {
      const idx = state.selectedBillingModels.indexOf(bm);
      if (idx > -1) {
        state.selectedBillingModels.splice(idx, 1);
      } else {
        state.selectedBillingModels.push(bm);
      }
      this.syncFilterCheckboxes();
      renderCatalog();
    },

    setPriceMax: function (val) {
      state.priceMax = parseInt(val) || 50000;
      const display = document.getElementById("price-max-display");
      if (display) display.value = state.priceMax;
      renderCatalog();
    },

    resetPriceFilter: function () {
      state.priceMax = 50000;
      const slider = document.getElementById("price-slider-input");
      const display = document.getElementById("price-max-display");
      if (slider) slider.value = 50000;
      if (display) display.value = 50000;
      renderCatalog();
    },

    resetAllFilters: function () {
      state.searchTerm = "";
      state.selectedCategories = [];
      state.selectedSectors = [];
      state.selectedAvailabilities = [];
      state.selectedBillingModels = [];
      state.priceMax = 50000;
      state.sortBy = "default";

      const searchInput = document.getElementById("catalog-search-input");
      if (searchInput) searchInput.value = "";

      const slider = document.getElementById("price-slider-input");
      const display = document.getElementById("price-max-display");
      if (slider) slider.value = 50000;
      if (display) display.value = 50000;

      this.syncFilterCheckboxes();
      renderCatalog();
      showToast("All filter criteria cleared", "teal");
    },

    syncFilterCheckboxes: function () {
      document.querySelectorAll(".filter-checkbox-input").forEach((input) => {
        const type = input.dataset.filterType;
        const val = input.dataset.filterValue;
        if (type === "category") {
          input.checked = state.selectedCategories.includes(val);
        } else if (type === "sector") {
          input.checked = state.selectedSectors.includes(val);
        } else if (type === "availability") {
          input.checked = state.selectedAvailabilities.includes(val);
        } else if (type === "billing") {
          input.checked = state.selectedBillingModels.includes(val);
        }
      });
    },

    setViewMode: function (mode) {
      state.viewMode = mode;
      document.querySelectorAll(".view-btn").forEach((btn) => {
        btn.classList.toggle("active", btn.dataset.mode === mode);
      });
      renderCatalog();
    },

    setSortBy: function (sortVal) {
      state.sortBy = sortVal;
      renderCatalog();
    },

    // Customer Switcher
    toggleCustomerDropdown: function (event) {
      if (event) event.stopPropagation();
      const menu = document.getElementById("customer-dropdown-menu");
      if (menu) menu.classList.toggle("show");
    },

    selectCustomer: function (customerId) {
      const cust = CUSTOMERS.find((c) => c.id === customerId);
      if (!cust) return;
      state.activeCustomer = cust;

      const codeEl = document.getElementById("header-customer-code");
      const nameEl = document.getElementById("header-customer-name");
      const avatarEl = document.getElementById("header-customer-avatar");

      if (codeEl) codeEl.innerText = cust.id;
      if (nameEl) nameEl.innerText = cust.name;
      if (avatarEl) avatarEl.innerText = cust.id.replace("CUS-", "");

      document.querySelectorAll(".customer-option-item").forEach((el) => {
        el.classList.toggle("selected", el.dataset.customerId === customerId);
      });

      const menu = document.getElementById("customer-dropdown-menu");
      if (menu) menu.classList.remove("show");

      showToast(
        `Switched active corporate account to: ${cust.name} (${cust.tier})`,
        "green",
      );

      if (state.currentScreen === "product-detail") {
        renderProductDetail(state.selectedProductId);
      }
    },

    submitDirectPO: function () {
      const poNumber =
        document.getElementById("po-number-input")?.value ||
        `PO-VP-${Math.floor(1000 + Math.random() * 9000)}`;
      showToast(
        `Purchase Order ${poNumber} submitted! Invoice and Statement of Work logged to Finance (System 07).`,
        "green",
      );
      state.cartItems = [];
      updateBadgeCounts();
      closeAllModals();
      switchScreen("tracking");
    },

    submitOfficialRfq: function () {
      showToast(
        `Quotation Request transmitted to Sales Account Manager ${state.activeCustomer.accountMgr}! Commercial Opportunity logged to CRM (System 05).`,
        "teal",
      );
      state.quoteItems = [];
      updateBadgeCounts();
      closeAllModals();
    },

    filterOrdersTable: function (query) {
      const q = (query || "").toLowerCase().trim();
      const rows = document.querySelectorAll("#tracking-orders-table-body tr");
      rows.forEach((row) => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(q) ? "" : "none";
      });
    },

    initEventListeners: function () {
      // Document click to close dropdowns
      document.addEventListener("click", () => {
        const menu = document.getElementById("customer-dropdown-menu");
        if (menu) menu.classList.remove("show");
      });

      // Filter Checkboxes change listener
      document.querySelectorAll(".filter-checkbox-input").forEach((input) => {
        input.addEventListener("change", (e) => {
          const type = e.target.dataset.filterType;
          const val = e.target.dataset.filterValue;
          if (type === "category") window.shopApp.toggleCategoryFilter(val);
          if (type === "sector") window.shopApp.toggleSectorFilter(val);
          if (type === "availability")
            window.shopApp.toggleAvailabilityFilter(val);
          if (type === "billing") window.shopApp.toggleBillingModelFilter(val);
        });
      });
    },
  };

  // Auto-initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => window.shopApp.init());
  } else {
    window.shopApp.init();
  }
})();
