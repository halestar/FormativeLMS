<?php

namespace Database\Seeders;

use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Database\Seeder;

class SkillCategorySeeder extends Seeder
{
    public array $categories =
        [
            [
                'label' => 'English',
                'children' =>
                [
                    [
                        'label' => 'Subject Area',
                        'items' =>
                        [
                            'English Language Arts & Literacy in History/Social Studies, Science, and Technical Subjects (K-5)',
                            'Literacy in History/Social Studies, Science, and Technical Subjects (6-12)',
                            'English Language Arts (6-12)',
                            'Literacy in History/Social Studies, Science, and Technical Subjects (6-12)'
                        ]
                    ],
                    [
                        'label' => 'Domain',
                        'items' =>
                        [
                            'Language',
                            'Speaking and Listening',
                        ],
                        'children' =>
                        [
                            [
                                'label' => 'Reading',
                                'items' =>
                                    [
                                        'Reading: Foundational Skills',
                                        'Reading: Informational Text',
                                        'Reading: Literature',
                                        'Reading: Literacy in History/Social Studies',
                                        'Reading: Literacy in Science and Technical',
                                    ]
                            ],
                            [
                                'label' => 'Writing',
                                'items' =>
                                [
                                    'Writing: Literacy in History/Social Studies, Science, and Technical Subjects',
                                ]
                            ]
                        ]
                    ],
                    [
                        'label' => 'Cluster',
                        'items' =>
                        [
                            'Comprehension and Collaboration',
                            'Conventions of Standard English',
                            'Craft and Structure',
                            'Fluency',
                            'Integration of Knowledge and Ideas',
                            'Key Ideas and Details',
                            'Knowledge of Language',
                            'Phonics and Word Recognition',
                            'Phonological Awareness',
                            'Presentation of Knowledge and Ideas',
                            'Print Concepts',
                            'Production and Distribution of Writing',
                            'Range of Reading and Level of Text Complexity',
                            'Range of Writing',
                            'Research to Build and Present Knowledge',
                            'Text Types and Purposes',
                            'Vocabulary Acquisition and Use',
                        ]
                    ]
                ]
            ],
            [
                'label' => 'Math',
                'children' =>
                [
                    [
                        'label' => 'Domain',
                        'items' =>
                        [
                            'Arithmetic with Polynomials and Rational Expressions',
                            'Building Functions',
                            'Circles',
                            'Conditional Probability and the Rules of Probability',
                            'Congruence',
                            'Counting and Cardinality',
                            'Creating Equations',
                            'Expressing Geometric Properties with Equations',
                            'Expressions and Equations',
                            'Functions',
                            'Geometric Measurement and Dimension',
                            'Geometry',
                            'Interpreting Categorical and Quantitative Data',
                            'Interpreting Functions',
                            'Linear, Quadratic, and Exponential Models',
                            'Making Inferences and Justifying Conclusions',
                            'Measurement and Data',
                            'Modeling with Geometry',
                            'Number and Operations in Base Ten',
                            'Number and Operations-Fractions',
                            'Operations and Algebraic Thinking',
                            'Quantities',
                            'Ratios and Proportional Relationships',
                            'Reasoning with Equations and Inequalities',
                            'Seeing Structure in Expressions',
                            'Similarity, Right Triangles, and Trigonometry',
                            'Statistics and Probability',
                            'The Complex Number System',
                            'The Number System',
                            'The Real Number System',
                            'Trigonometric Functions',
                            'Using Probability to Make Decisions',
                        ],
                    ],
                    [
                        'label' => 'Discipline',
                        'items' =>
                        [
                            'Algebra I',
                            'Algebra II',
                            'Calculus',
                            'Geometry',
                            'Math I',
                            'Math II',
                            'Math III',
                            'Statistics and Probability',
                            'Statistics and Probability (AP)',
                        ]
                    ],
                    [
                        'label' => 'Conceptual Category',
                        'items' =>
                        [
                            'Algebra',
                            'Functions',
                            'Geometry',
                            'Number and Quantity',
                            'Statistics and Probability',
                        ]
                    ]
                ]
            ],
            [
                'label' => 'Science',
                'children' =>
                [
                    [
                        'label' => 'Disciplinary Core Idea',
                        'children' =>
                        [
                            [
                                'label' => 'ESS1',
                                'items' =>
                                [
                                    'ESS1.A: The Universe and its Stars',
                                    'ESS1.A: The Universe and its Stars, ESS1.B: Earth and the Solar System',
                                    'ESS1.A: The Universe and its Stars, PS3.D: Energy in Chemical Processes',
                                    'ESS1.A: The Universe and its Stars, PS4.B: Electromagnetic Radiation',
                                    'ESS1.B: Earth and the Solar System',
                                    'ESS1.B: Earth and the Solar System, ESS2.A: Earth Materials and Systems, ESS2.D: Weather and Climate',
                                    'ESS1.C: The History of Planet Earth',
                                    'ESS1.C: The History of Planet Earth, ESS2.B: Plate Tectonics and Large-Scale System Interactions',
                                    'ESS1.C: The History of Planet Earth, ESS2.B: Plate Tectonics and Large-Scale System Interactions, PS1.C: Nuclear Processes',
                                    'ESS1.C: The History of Planet Earth, PS1.C: Nuclear Processes',
                                ]
                            ],
                            [
                                'label' => 'ESS2',
                                'items' =>
                                    [
                                        "ESS2.A: Earth Materials and Systems",
                                        "ESS2.A: Earth Materials and Systems, ESS2.B: Plate Tectonics and Large-Scale System Interactions",
                                        "ESS2.A: Earth Materials and Systems, ESS2.B: Plate Tectonics and Large-Scale System Interactions, PS4.A: Wave Properties",
                                        "ESS2.A: Earth Materials and Systems, ESS2.C: The Roles of Water in Earth's Surface Processes",
                                        "ESS2.A: Earth Materials and Systems, ESS2.D: Weather and Climate",
                                        "ESS2.A: Earth Materials and Systems, ETS1.C: Optimizing the Design Solution",
                                        "ESS2.B: Plate Tectonics and Large-Scale System Interactions",
                                        "ESS2.C: The Roles of Water in Earth's Surface Processes",
                                        "ESS2.C: The Roles of Water in Earth's Surface Processes, ESS2.D: Weather and Climate",
                                        "ESS2.D: Weather and Climate",
                                        "ESS2.E: Biogeology",
                                    ]
                            ],
                            [
                                'label' => 'ESS3',
                                'items' =>
                                    [
                                        "ESS3.A: Natural Resources",
                                        "ESS3.B: Natural Hazards",
                                        "ESS3.C: Human Impacts on Earth Systems",
                                        "ESS3.D: Global Climate Change",
                                    ]
                            ],
                            [
                                'label' => 'ETS1',
                                'items' =>
                                    [
                                        "ETS1.A: Defining and Delimiting Engineering Problems",
                                        "ETS1.B: Developing Possible Solutions",
                                        "ETS1.C: Optimizing the Design Solution",
                                    ]
                            ],
                            [
                                'label' => 'LS1',
                                'items' =>
                                    [
                                        "LS1.A: Structure and Function",
                                        "LS1.A: Structure and Function, LS1.D: Information Processing",
                                        "LS1.A: Structure and Function, LS3.A: Inheritance of Traits",
                                        "LS1.B: Growth and Development of Organisms",
                                        "LS1.B: Growth and Development of Organisms, LS3.A: Inheritance of Traits, LS3.B: Variation of Traits",
                                        "LS1.C: Organization for Matter and Energy Flow in Organisms",
                                        "LS1.C: Organization for Matter and Energy Flow in Organisms, PS3.D: Energy in Chemical Processes",
                                        "LS1.D: Information Processing",
                                    ]
                            ],
                            [
                                'label' => 'LS2',
                                'items' =>
                                    [
                                        "LS2.A: Interdependent Relationships in Ecosystems",
                                        "LS2.A: Interdependent Relationships in Ecosystems, ETS1.B: Developing Possible Solutions",
                                        "LS2.A: Interdependent Relationships in Ecosystems, LS2.B: Cycles of Matter and Energy Transfer in Ecosystems",
                                        "LS2.A: Interdependent Relationships in Ecosystems, LS2.C: Ecosystem Dynamics, Functioning, and Resilience",
                                        "LS2.B: Cycles of Matter and Energy Transfer in Ecosystems",
                                        "LS2.B: Cycles of Matter and Energy Transfer in Ecosystems, PS3.D: Energy in Chemical Processes",
                                        "LS2.C: Ecosystem Dynamics, Functioning, and Resilience",
                                        "LS2.C: Ecosystem Dynamics, Functioning, and Resilience, LS4.D: Biodiversity and Humans",
                                        "LS2.C: Ecosystem Dynamics, Functioning, and Resilience, LS4.D: Biodiversity and Humans, ETS1.B: Developing Possible Solutions",
                                        "LS2.D: Social Interactions and Group Behavior",
                                    ]
                            ],
                            [
                                'label' => 'LS3',
                                'items' =>
                                    [
                                        "LS3.A: Inheritance of Traits",
                                        "LS3.B: Variation of Traits",
                                    ]
                            ],
                            [
                                'label' => 'LS4',
                                'items' =>
                                    [
                                        "LS4.A: Evidence of Common Ancestry and Diversity",
                                        "LS4.B: Natural Selection",
                                        "LS4.C: Adaptation",
                                        "LS4.D: Biodiversity and Humans",
                                    ]
                            ],
                            [
                                'label' => 'PS1',
                                'items' =>
                                    [
                                        "PS1.A: Structure and Properties of Matter",
                                        "PS1.B: Chemical Reactions",
                                        "PS1.C: Nuclear Processes",
                                    ]
                            ],
                            [
                                'label' => 'PS2',
                                'items' =>
                                    [
                                        "PS2.A: Forces and Motion",
                                        "PS2.B: Types of Interactions",
                                    ]
                            ],
                            [
                                'label' => 'PS3',
                                'items' =>
                                    [
                                        "PS3.A: Definitions of Energy",
                                        "PS3.B: Conservation of Energy and Energy Transfer",
                                        "PS3.C: Relationship between Energy and Forces",
                                        "PS3.D: Energy in Chemical Processes",
                                    ]
                            ],
                            [
                                'label' => 'PS4',
                                'items' =>
                                    [
                                        "PS4.A: Wave Properties",
                                        "PS4.B: Electromagnetic Radiation",
                                        "PS4.C: Information Technologies and Instrumentation",
                                    ]
                            ],
                        ],
                    ],
                    [
                        'label' => 'Cross Cutting Concepts',
                        'items' =>
                        [
                            "CCC-1: Patterns",
                            "CCC-3: Scale, Proportion, and Quantity",
                            "CCC-5: Energy and Matter: Flows, Cycles, and Conservation",
                            "CCC-4: Systems and Systems Models",
                            "CCC-2: Cause and Effect: Mechanism and Explanation",
                            "CCC-7: Stability and Change",
                            "CCC-6: Structure and Function",
                        ]
                    ],
                    [
                        'label' => 'Science & Engineering Practice',
                        'items' =>
                            [
                                "SEP-4: Analyzing and Interpreting Data",
                                "SEP-7: Engaging in Argument From Science",
                                "SEP-8: Obtaining, Evaluating, and Communicating Information",
                                "SEP-2: Developing and Using Models",
                                "SEP-6: Constructing Explanations and Designing Solutions",
                                "SEP-3: Planning and Carrying Out Investigations",
                                "SEP-5: Using Mathematics and Computational Thinking",
                                "SEP-1: Asking Questions and Defining Problems",
                            ]
                    ],
                    [
                        'label' => 'Content Area',
                        'items' =>
                            [
                                "Earth and Space Science",
                                "Engineering, Technology, and Applications of Science",
                                "Life Science",
                                "Physical Science",
                            ]
                    ],
                ]
            ],
            [
                'label' => 'Social Studies',
                'items' =>
                [
                    "Learning and Working Now and Long Ago, Grade K",
                    "A Child's Place in Time and Space, Grade 1",
                    "People Who Make a Difference, Grade 2",
                    "Continuity and Change, Grade 3",
                    "California: A Changing State, Grade 4",
                    "United States History and Geography: Making a New Nation, Grade 5",
                    "World History and Geography: Ancient Civilizations, Grade 6",
                    "World History and Geography: Medieval and Early Modern Times, Grade 7",
                    "United States History and Geography: Growth and Conflict, Grade 8",
                    "World History, Culture, and Geography: The Modern World, Grade 10",
                    "United States History and Geography: Continuity and Change in the Twentieth Century, Grade 11",
                    "Principles of American Democracy, Grade 12",
                    "Principles of Economics, Grade 12",
                ]
            ],
            [
                'label' => 'Art',
                'children' =>
                    [
                        [
                            'label' => 'Discipline',
                            'items' =>
                            [
                                "Dance",
                                "Media Arts",
                                "Music",
                                "Theatre",
                                "Visual Arts",
                                "Music",
                            ]
                        ],
                        [
                            'label' => 'Subdiscipline',
                            'items' =>
                                [
                                    "TK-8",
                                    "Harmonizing Instruments",
                                    "Ensembles",
                                    "Composition and Theory",
                                    "Technology",
                                ]
                        ],
                        [
                            'label' => 'Artistic Process',
                            'items' =>
                                [
                                    "Creating",
                                    "Performing",
                                    "Responding",
                                    "Connecting",
                                    "Producing (Media Arts only)",
                                    "Presenting (Visual Arts only)",
                                ]
                        ],
                        [
                            'label' => 'Anchor Standard',
                            'items' =>
                                [
                                    "1: Generate and conceptualize artistic ideas and work",
                                    "2: Organize and develop artistic ideas and work",
                                    "3: Refine and complete artistic work",
                                    "4. Select, analyze, and interpret artistic work for presentation",
                                    "5: Develop and refine artistic techniques and work for presentation",
                                    "6: Convey meaning through the presentation of artistic work",
                                    "7: Perceive and analyze artistic work",
                                    "8: Interpret intent and meaning in artistic work",
                                    "9: Apply criteria to evaluate artistic work",
                                    "10: Synthesize and relate knowledge and personal experiences to make art",
                                    "11: Relate artistic ideas and works with societal, cultural, and historical context to deepen understanding",
                                    "7: Perceive and analyze artistic work ",
                                    "11: Relate artistic ideas and works with societal, cultural, and historical context to deepen understanding",
                                ]
                        ],
                    ]
            ],
            [
                'label' => 'Health',
                'children' =>
                    [
                        [
                            'label' => 'Content Area',
                            'items' =>
                                [
                                    "Alcohol, Tobacco, and Other Drugs",
                                    "Growth and Development",
                                    "Mental, Emotional, and Social Health",
                                    "Nutrition and Physical Activity",
                                    "Personal and Community Health",
                                    "Injury, Prevention, and Safety",
                                    "Growth, Development, and Sexual Health",
                                ]
                        ],
                        [
                            'label' => 'Overarching Content Standard',
                            'items' =>
                                [
                                    "1: Essential Concepts",
                                    "2: Analyzing Influences",
                                    "3: Accessing Valid Health Information",
                                    "4: Interpersonal Communication",
                                    "5: Decision Making",
                                    "6: Goal Setting",
                                    "7: Practicing Health-Enhancing Behaviors",
                                    "8: Health Promotion",
                                ]
                        ],
                    ],
            ],
            [
                'label' => 'Languages',
                'children' =>
                    [
                        [
                            'label' => 'Strand',
                            'items' =>
                                [
                                    "Communication",
                                    "Cultures",
                                    "Connections",
                                ]
                        ],
                        [
                            'label' => 'Substrand',
                            'items' =>
                                [
                                    "Interpretive Communication",
                                    "Interpersonal Communication",
                                    "Presentational Communication",
                                    "Settings for Communication",
                                    "Receptive Structures in Service of Communication",
                                    "Productive Structures in Service of Communication",
                                    "Language Comparisons in Service of Communication",
                                    "Culturally Appropriate Interaction",
                                    "Cultural Products, Practices, and Perspectives",
                                    "Cultural Comparisons",
                                    "Intercultural Influences",
                                    "Connections to Other Disciplines",
                                    "Diverse Perspectives and Distinctive Viewpoints",
                                ]
                        ],
                    ],
            ],
        ];

    public function insertPath(array $category, ?int $parent_id)
    {
        //first, we enter this category.
        $cat = SkillCategory::create
        (
            [
                'parent_id' => $parent_id,
                'name' => $category['label'],
            ]
        );
        //next, if there are any items, enter them here.
        if(isset($category['items']))
        {
            foreach ($category['items'] as $item)
            {
                SkillCategory::create
                (
                    [
                        'parent_id' => $cat->id,
                        'name' => $item,
                    ]
                );
            }
        }
        //finally, the recursive part, we recurse through all children.
        if(isset($category['children']))
        {
            foreach($category['children'] as $child)
            {
                $this->insertPath($child, $cat->id);
            }
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->categories as $category)
        {
            $this->insertPath($category, null);
        }
    }
}
